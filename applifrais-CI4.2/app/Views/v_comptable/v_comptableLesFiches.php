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
            $comment = '';
            $valideLink = '';
            $refuseLink = '';

            if (isset($_POST['comment'])) {
            $comment = $_POST['comment'];
            }
            
            

            if ($uneFiche['id'] == 'CL') {
            // modification de signelink en valideLink avec un apramètre idVisiteur en plus.
            $valideLink = anchor('comptable/validerFiche/' . $uneFiche['mois'] . '/' . $uneFiche['idVisiteur'], 'valider', 'title="valider la fiche"');
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $refuseLink = anchor('comptable/refuserFiche/' . $uneFiche['mois'] . '/' . $uneFiche['idVisiteur'] . '/' . $comment, 'refuser', 'title="refuser la fiche"');
            }
            }

            echo
            '<tr>
					<td class="date">' . anchor('comptable/voirLaFiche/' . $uneFiche['mois'], $uneFiche['mois'], 'title="Consulter la fiche"') . '</td>
					<td class="idVisiteur">' . $uneFiche['idVisiteur'] . '</td>
                                        <td class="libelle">' . $uneFiche['libelle'] . '</td>
					<td class="montant">' . $uneFiche['montantValide'] . '</td>
					<td class="date">' . $uneFiche['dateModif'] . '</td>
					<td class="action">' . $valideLink . '</td>
                                        <td class="action">' .  $refuseLink . ' </br>'
            . '<form id="refuseForm_" method="post">
                                       <p>Commentaire : <input type="text" name="comment" required/></p>
                                       <p><input type="submit" value="valider Commentaire"></p>
                                       </form></td>
                                            </tr>';
            }
            ?>	  
        </tbody>
    </table>

</div>
<?= $this->endSection() ?>