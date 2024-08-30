<!-- Content Wrapper. Contains page content -->
  <div class="content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Liste des comptes-rendus</h1>
          </div>
          <div class="col-sm-6">
          <form class="" action="" method="get">
              <input type="search" size="20" name="recherche" value="<?php if (isset($_GET['recherche'])) {
                echo $_GET['recherche'];
              } ?>" placeholder="Recherchez par objret" required>
              <button type="submit" name="bt_rech">Recherchez</button>
          </form>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <?php
                //selectionnons le matricule de $username
                $sql="SELECT matricule FROM utilisateurs
                            WHERE mail='$username' AND is_deleted=FALSE
                            ";
                    $exe= $con->query($sql);
                    $row = $exe->fetch(PDO::FETCH_ASSOC);
                    $matricule = $row['matricule'];
                ?>
                <table id="table" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Directions concernées</th>
                    <th>Date de la reunion</th>
                    <th>Heure de la reunion</th>
                    <th>Ordre du jour</th>
                    <th>Compte rendu</th>
                  </tr>
                  </thead>
                  <tbody>
<!--         code php pour afficher la liste des comptes rendus     -->
<?php
if ($directeurConnect) {
      if (isset($_GET['recherche'])) { //recherche est le name de l'input
            $champRecherchre= $_GET['recherche'];
            $requete="SELECT * FROM directions AS di
                     INNER JOIN reunions AS r
                     ON di.id_direction=r.directions_id
                      INNER JOIN designer AS d
                    ON r.id_reunion = d.reunion_id
        WHERE (date_reunion LIKE '%$champRecherchre%' OR
                            r.ordre_du_jour LIKE '%$champRecherchre%')
                            AND r.compte_rendu IS NOT NULL
                            AND d.role_joue='Chargé du compte-rendu'
                    ORDER BY date_reunion DESC";
            }elseif (isset($_GET['parametre'])) { //recherche est le name de l'input
              $requete="SELECT * FROM directions AS di
                       INNER JOIN reunions AS r
                       ON di.id_direction=r.directions_id
                INNER JOIN designer AS d
              ON r.id_reunion = d.reunion_id
              INNER JOIN notifications AS n
             ON r.id_reunion = n.reunion_id
                    WHERE n.date_notification BETWEEN DATE_SUB('$dateFormat', INTERVAL 6 DAY) AND '$dateFormat'
                    AND r.compte_rendu IS NOT NULL
                    AND d.role_joue='Chargé du compte-rendu'
                          AND contenu_notification LIKE 'Un compte%'
                          ";
                  }
      else
          {
            $requete="SELECT * FROM directions AS di
                     INNER JOIN reunions AS r
                     ON di.id_direction=r.directions_id
                  INNER JOIN designer AS d
                  ON r.id_reunion = d.reunion_id
                  WHERE r.compte_rendu IS NOT NULL
                  AND d.role_joue='Chargé du compte-rendu'
                  ORDER BY r.date_reunion DESC";
        }
 }else {  //non $directeurConnect.........................................................................
   if (isset($_GET['recherche'])) {
         $champRecherchre= $_GET['recherche'];
         $requete="SELECT * FROM directions AS di
                  INNER JOIN reunions AS r
                  ON di.id_direction=r.directions_id
                 INNER JOIN designer AS d
                 ON r.id_reunion = d.reunion_id
               WHERE (date_reunion LIKE '%$champRecherchre%' OR
                         r.ordre_du_jour LIKE '%$champRecherchre%')
                         AND (di.id_direction=".$rowId['id_direction']." or di.id_direction=0)
                         AND r.compte_rendu IS NOT NULL
                         AND d.role_joue='Chargé du compte-rendu'
             ORDER BY date_reunion DESC";
         }elseif (isset($_GET['parametre'])) { //recherche est le name de l'input
           $requete="SELECT * FROM directions AS di
                    INNER JOIN reunions AS r
                    ON di.id_direction=r.directions_id
                    INNER JOIN designer AS d
                    ON r.id_reunion = d.reunion_id
                  INNER JOIN notifications AS n
                 ON r.id_reunion = n.reunion_id
                 WHERE n.date_notification BETWEEN DATE_SUB('$dateFormat', INTERVAL 6 DAY) AND '$dateFormat'
                 AND r.compte_rendu IS NOT NULL
                 AND (di.id_direction=".$rowId['id_direction']." or di.id_direction=0)
                 AND d.role_joue='Chargé du compte-rendu'
                       AND contenu_notification LIKE 'Un compte%'
                       ";
               }
      else
          {
            $requete="SELECT * FROM directions AS di
                INNER JOIN reunions AS r
                ON di.id_direction=r.directions_id
               INNER JOIN designer AS d
               ON r.id_reunion = d.reunion_id
               WHERE r.compte_rendu IS NOT NULL
               AND (di.id_direction=".$rowId['id_direction']." or di.id_direction=0)
               AND d.role_joue='Chargé du compte-rendu'
               ORDER BY r.date_reunion DESC";
     }
 }
  $exe_requete= $con->query($requete);
      if ($exe_requete->rowCount() > 0) {
         while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
             // formatage de la date
             setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra'); // Définit la localisation en français
             $dateReunion = $row['date_reunion'];
             $DateTime = new DateTime($dateReunion);
             $jourSemaine = utf8_encode(strftime('%A', $DateTime->getTimestamp())); // %A pour le nom complet du jour de la semaine
             $jour = $DateTime->format('d');
             $mois = utf8_encode(strftime('%B', $DateTime->getTimestamp())); // pour le nom complet du mois
             $annee = $DateTime->format('Y');

             // formatage de l'heure
             $direction = $row['libelle_direction'];
             $heureDebut = $row['heure_debut'];
             $heureDebutTime = new DateTime($heureDebut);
             $heureDebutFormat = $heureDebutTime->format('G \H i');

             $ordre = $row['ordre_du_jour'];
             $compte = $row['compte_rendu'];
             $idReunion = $row['id_reunion'];

             echo "
   <tr>
       <td>{$direction}</td>
       <td>{$jourSemaine} {$jour} {$mois} {$annee}</td>
       <td>{$heureDebutFormat}</td>
       <td>{$ordre}</td>
       <td><a href=\"{$compte}\">Télécharger le Compte-rendu</a> ";
   // En cas de modification ou suppression
   if ($row['matricule_designer'] == $matricule) {
   $urlMod = "ajout-de-comptes-rendus.php?modCompte=" . urlencode($idReunion);
   $urlSup = "traitement.php?supCompte=" . urlencode($idReunion);
   echo "&nbsp;&nbsp;&nbsp;<a href='{$urlMod}'><i class='fas fa-pen text-success'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;";
   echo "<a href='{$urlSup}' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce compte-rendu ?');\"><i class='fas fa-trash text-danger'></i></a>";
}
     }
     echo "
         </td>
    </tr>";
   }
else {
                echo "<center><h4 class='text-danger'> Aucun compte-rendu disponible </h4></center>";
      }
    ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
      <br><br>  <center>
                          <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <a href="reunions.php"><button type="button" name="button">Toutes les réunions</button></a>
                </center>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
