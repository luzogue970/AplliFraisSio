<?php namespace App\Controllers;

use CodeIgniter\Controller;
use \App\Models\Authentif;

class Anonyme extends BaseController
{
	public function index()
	{
		$authentif = new Authentif();
		if (!$authentif->estConnecte()) 
		{
			$data = array();
			return view('v_connexion', $data);
		}
//                vérification du statut de l'utilisateur : comptable ou non
                else if ($authentif->estComptable() == true) {
                    return redirect()->to('/Comptable');
                }
                else if ($authentif->estComptable() == false) {
                    return redirect()->to('/Visiteur');
                }
	}

	/**
	 * Traite le retour du formulaire de connexion afin de connecter l'utilisateur
	 * s'il est reconnu
	*/
	public function seConnecter () 
	{	// TODO : conrôler que l'obtention des données postées ne rend pas d'erreurs 

		$login = $this->request->getPost('login');
		$mdp = $this->request->getPost('mdp');
		
		$authentif = new Authentif();
		$authUser = $authentif->authentifier($login, $mdp);

		if(empty($authUser))
		{
			$data = array('erreur'=>'Login ou mot de passe incorrect');
			return view('v_connexion', $data);
		}
                    else
		{
			$authentif->connecter($authUser['id'], $authUser['nom'], $authUser['prenom'],$authUser['statut']);
			return $this->index();
		}
	}
}