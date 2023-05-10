<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use \App\Models\Authentif;
use App\Models\ActionsComptable;

/**
 * Contrôleur du module COMPTABLE de l'application
 */
class Comptable extends BaseController {

    private $authentif;
    private $actComptable;
    private $idComptable;

    private function checkAuth() {
        // contrôle de la bonne authentification de l'utilisateur
        // TODO : 	Lorsque des comptables utiliseront cette même application, il faudra enrichir 
        //			ce code. En effet les comptables n'ont pas le droit d'accéder à ce code et, par
        //			ailleurs, les visiteurs n'auront pas d'accès au controleur des comptables !!!
        $this->authentif = new Authentif();
        if (!$this->authentif->estConnecte()) {
            $res = false;
        } else {
            $this->actComptable = new ActionsComptable();

            $this->session = session();
            $this->idComptable = $this->session->get('idUser');
            $this->actComptable->checkLastSix($this->idComptable);
            $res = true;
        }
        return $res;
    }

    private function unauthorizedAccess() {
        // l'accès à ce contrôleur n'est pas autorisé : on renvoie une vue erreur
        return view('errors/html/error_401');
        // on aurait aussi  pu renvoyer vers le contrôleur par défaut comme suit : 
        //return redirect()->to('/applifrais-CI4/public/anonyme');
    }

    public function index() {
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        // envoie de la vue accueil du Comptable
        $data['identite'] = $this->session->get('prenom') . ' ' . $this->session->get('nom');

        return view('v_comptable/v_comptableAccueil', $data);
    }

    public function lesFiches($message = "") {
//        renvoie la vue permettant de voir tous les fiches signées pour le comptable
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        $data['identite'] = $this->session->get('prenom') . ' ' . $this->session->get('nom');
        $data['lesFiches'] = $this->actComptable->getLesFichesComptables($this->idComptable);
        $data['notify'] = $message;

        return view('v_comptable/V_comptableLesFiches', $data);
    }

    public function seDeconnecter() {
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        return $this->authentif->deconnecter();
    }

    public function voirLaFiche($mois) { // TODO : contrôler la validité du paramètre (mois de la fiche à consulter)
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        $data['identite'] = $this->session->get('prenom') . ' ' . $this->session->get('nom');
        $data['mois'] = $mois;
        $data['fiche'] = $this->actComptable->getUneFiche($this->idComptable, $mois);

        return view('v_comptable/v_comptableVoirFiche', $data);
    }
    
    /**
     * validerFiche
     *
     * @param  mixed $mois
     * @param  mixed $idVisiteur
     * @return void
     */
    public function validerFiche($mois, $idVisiteur) { // TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)
//        peremt au comptable de valider la fiche
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        $this->actComptable->validerFiche($idVisiteur, $mois);

//        renvoie à à la vue LesFiches avec un message de confirmation
        return $this->lesFiches("La fiche $mois a été validée.");
    }
    
    /**
     * refuserFiche
     *
     * @param  mixed $mois
     * @param  mixed $idVisiteur
     * @param  mixed $comment
     * @return void
     */
    public function refuserFiche($mois,$idVisiteur) { // TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)
//        permet au comptable de refuser les fiches avec un message
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        
        		$comment = $this->request->getPost('comment');
        $this->actComptable->refuserFiche($idVisiteur,$mois,$comment);
//        renvoie à à la vue LesFiches avec un message de confirmation
        return $this->lesFiches("La fiche $mois a été refusé pour le motif $comment");
    }

    public function majForfait($mois) { // TODO : conrôler que l'obtention des données postées ne rend pas d'erreurs
        // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        // obtention des données postées
        $lesFrais = $this->request->getPost('lesFrais');

        $this->actComptable->majForfait($this->idComptable, $mois, $lesFrais);

        // ... et on revient en modification de la fiche
        return $this->modMaFiche($mois, 'Modification(s) des éléments forfaitisés enregistrée(s) ...');
    }

    public function ajouteUneLigneDeFrais($mois) { // TODO : conrôler que l'obtention des données postées ne rend pas d'erreurs
        // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        // obtention des données postées
        $uneLigne = array(
            'dateFrais' => $this->request->getPost('dateFrais'),
            'libelle' => $this->request->getPost('libelle'),
            'montant' => $this->request->getPost('montant')
        );
        $this->actComptable->ajouteFrais($this->idComptable, $mois, $uneLigne);

        // ... et on revient en modification de la fiche
        return $this->modMaFiche($mois, 'Ligne "Hors forfait" ajoutée ...');
    }

    public function supprUneLigneDeFrais($mois, $idLigneFrais) { // TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)
        // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche
        if (!$this->checkAuth())
            return $this->unauthorizedAccess();
        // l'id de la ligne à supprimer doit avoir été transmis en second paramètre
        $this->actComptable->supprFrais($this->idComptable, $mois, $idLigneFrais);

        // ... et on revient en modification de la fiche
        return $this->modMaFiche($mois, 'Ligne "Hors forfait" supprimée ...');
    }

}
