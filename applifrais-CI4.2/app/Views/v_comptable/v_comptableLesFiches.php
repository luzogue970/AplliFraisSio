<?= $this->extend('v_comptable/l_comptable') ?>

<?= $this->section('body') ?>
<div id="contenu">
    <h2>Liste des fiches de frais signées</h2>

    <?php if (!empty($notify)) echo '<p id="notify" >' . $notify . '</p>'; ?>
    <table class="listeLegere">
        <thead>
            <tr>
                <th >Mois</th>
                <th >idVisiteur</th>
                <th >Etat</th>  
                <th >Montant</th>  
                <th >Date modif.</th>  
                <th  colspan="4">Actions</th>              
            </tr>
        </thead>
        <tbody>

            <?php
            foreach ($lesFiches as $uneFiche) {
//                création de $comment qui recevra le commentaire 
                $comment = '';
                //permet d'attendre que la requête soit envoyé pour donner sa valeur à commentaire
                if (isset($_POST['comment'])) {
                    $comment = $_POST['comment'];
                }
                $valideLink = '';
                $refuseLink = '';

//            n'affiches les deux boutons seulement si la l'id de la fiche est CL
                if ($uneFiche['id'] == 'CL') {
                    // création de valideLink qui permet de valider la fiche d'un utilisateur.
                    $valideLink = anchor('comptable/validerFiche/' . $uneFiche['mois'] . '/' . $uneFiche['idVisiteur'], 'valider', 'title="valider la fiche"');
                }
                echo
                '<tr>
					<td class="date">' . anchor('comptable/voirLaFiche/' . $uneFiche['mois'], $uneFiche['mois'], 'title="Consulter la fiche"') . '</td>
					' . /* Ajout d'une ligne id_visiteur pour plsu de lisibilité pour le comtpable */'
                                        <td class="idVisiteur">' . $uneFiche['idVisiteur'] . '</td>
                                        <td class="libelle">' . $uneFiche['libelle'] . '</td>
					<td class="montant">' . $uneFiche['montantValide'] . '</td>
					<td class="date">' . $uneFiche['dateModif'] . '</td>
					<td class="action">' . $valideLink . '</td>
                                        <td class="action">' . $refuseLink . ' </br>';?>
                <!--/*form permettant de mettre un commentaire de l'envoyer par la méthode post + execution de refuserfiche*/-->
            <form method="post" action="<?= site_url() . 'comptable/refuserFiche/' . $uneFiche['mois'] . '/' . $uneFiche['idVisiteur'] ?>">
                <p>Commentaire : <input type="text" name="comment" required/></p>
                <p><input type="submit" value="refuser"></p>
            </form></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
<?= $this->endSection() ?>