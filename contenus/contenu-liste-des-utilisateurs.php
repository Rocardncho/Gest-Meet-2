<!-- Content Wrapper. Contains page content -->
<div class="content">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Liste des utilisateurs</h1>
        </div>
        <div class="col-sm-6">
          <form class="d-flex" action="" method="get">
            <input type="search" size="30" name="recherche" value="<?php if (isset($_GET['recherche'])) {
              echo $_GET['recherche'];
            } ?>" placeholder="Recherchez par nom, prénom, téléphone, email, poste ou par direction" required>
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
              <?php  if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
              <a href="ajout-utilisateur.php">
                <h3 class="card-title">Ajouter utilisateurs</h3>
              </a>
            <?php } ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <!--************ Affichage du poste de directeur ******************* -->
              <div class="">
                <table id="table">
                  <?php
                      $requete="SELECT *  FROM utilisateurs AS u
                                  INNER JOIN postes AS p ON
                                  u.poste_id=p.id_poste
                             WHERE u.poste_id=0 AND u.is_deleted=FALSE";
                      $exe_requete= $con->query($requete);
                     if ($exe_requete->rowCount()>0) {
                       // code...
                         $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                         $poste= $row['libelle_poste'];
                         $matriculeDirecteur= $row['matricule'];
                         $matricule= $row['matricule'];
                         $nom= $row['nom'];
                         $prenom= $row['prenom'];
                         $contact= $row['contact'];
                         $email= $row['mail'];
                   ?>
                  <tr>
                    <?php
                       $url="liste-des-utilisateurs.php?directeur=".urlencode($matriculeDirecteur);
                    ?>
                    <th>
                      <h4 style="color:#550000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $poste ?>:</h4>
                    </th>
                    <th>
                      <h4 style="color:#444444;">&nbsp;&nbsp;&nbsp;<?php echo $nom."  ".$prenom; ?>&nbsp;&nbsp;
                          <?php if ($directeurConnect || $adminRole) { ?>
                        <a href="<?php echo $url ?>">
                          <i class="fa-solid fa-eye"></i>
                        </a>
                      <?php } ?>
                      </h4>
                    </th>
                    <?php if ($directeurConnect || $adminRole) { ?>
                    <th>
                      <?php
                         $url="ajout-utilisateur.php?id=".urlencode($matriculeDirecteur);
                      ?>
                      <div class="">
                        &nbsp;&nbsp;&nbsp;<a href="<?php echo $url ?>"><i class="fas fa-pen text-success">Modifier</i></a>
                      </div>
                    </th>
                  <?php } ?>
                  </tr>
                </table>
              </div>
              <?php }// FIN if ($directeurConnect || $adminRole) ?>
              <table id="table" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <?php
                    if (!isset($_GET['directeur'])) {
                        echo"<th>Sous-direction</th>";
                      }else {
                        echo"<th>Direction</th>";
                      }
                      ?>
                    <th>Poste</th>
                    <th>Mise à jour</th>
                    <?php  if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
                    <th>
                      <div class="d-flex justify-content-between">
                        <div class="text-success">Modifier</div>
                        <?php  if (!isset($_GET['directeur'])) { ?>
                        <div class="text-danger">Supprimer</div>
                        <?php } ?>
                      </div>
                    </th>
                  <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <!--         code php pour afficher la liste des comptes rendus     -->
                  <?php
                  if ($adminRole || $directeurConnect) { // Si c'st l'administrateur ou le directeur
                        if (isset($_GET['recherche'])) { //recherche est le name de l'input
                              $champRecherchre= urlencode($_GET['recherche']);

                          $requete="SELECT *
                          FROM utilisateurs AS u
                          INNER JOIN postes AS p ON
                          u.poste_id=p.id_poste
                          INNER JOIN directions AS d ON
                          p.directions_id=d.id_direction
                            WHERE (nom LIKE '%$champRecherchre%' OR
                                              prenom LIKE '%$champRecherchre%' OR
                                              matricule LIKE '%$champRecherchre%'OR
                                              libelle_direction LIKE '%$champRecherchre%' OR
                                              contact LIKE '%$champRecherchre%'OR
                                              libelle_poste LIKE '%$champRecherchre%'
                                            ) AND u.is_deleted=FALSE
                                              ORDER BY nom";
                              }elseif (isset($_GET['parametre'])) {
                                $requete= "SELECT * FROM utilisateur AS u
                                      INNER JOIN notifications AS n
                                      ON r.id_reunion = n.reunion_id
                                      WHERE n.date_notification='$dateFormat'
                                            AND contenu_notification LIKE 'Un compte%'";
                                  }elseif (isset($_GET['directeur'])) {
                                    $requete="SELECT u.nom, u.prenom, u.matricule, u.contact,
                                                      u.mail, u.mise_a_jour, p.libelle_poste,
                                                      d.libelle_direction
                                      FROM utilisateurs AS u
                                    INNER JOIN postes AS p ON
                                    u.poste_id=p.id_poste
                                    INNER JOIN directions AS d ON
                                    p.directions_id=d.id_direction
                                    WHERE u.poste_id=0 AND u.is_deleted = FALSE
                                     ";
                                  }
                       else
                          {
                          $requete="SELECT u.nom, u.prenom, u.matricule, u.contact,
                                            u.mail, u.mise_a_jour, p.libelle_poste,
                                            d.libelle_direction
                            FROM utilisateurs AS u
                          INNER JOIN postes AS p ON
                          u.poste_id=p.id_poste
                          INNER JOIN directions AS d ON
                          p.directions_id=d.id_direction
                          WHERE u.poste_id<>0 AND u.is_deleted = FALSE
                           ORDER BY d.libelle_direction, nom";
                        }
                  }
                  if (!$adminRole && !$directeurConnect) { // Si c'st l'administrateur ou le directeur
                        if (isset($_GET['recherche'])) { //recherche est le name de l'input
                              $champRecherchre= urlencode($_GET['recherche']);

                          $requete="SELECT *
                          FROM utilisateurs AS u
                          INNER JOIN postes AS p ON
                          u.poste_id=p.id_poste
                          INNER JOIN directions AS d ON
                          p.directions_id=d.id_direction
                            WHERE (nom LIKE '%$champRecherchre%' OR
                                              prenom LIKE '%$champRecherchre%' OR
                                              matricule LIKE '%$champRecherchre%'OR
                                              libelle_direction LIKE '%$champRecherchre%' OR
                                              contact LIKE '%$champRecherchre%'OR
                                              libelle_poste LIKE '%$champRecherchre%'
                                            ) AND u.is_deleted=FALSE AND d.libelle_direction='".$rowId['libelle_direction']."'
                                              ORDER BY nom";
                              }elseif (isset($_GET['parametre'])) {
                                $requete= "SELECT * FROM utilisateur AS u
                                      INNER JOIN notifications AS n
                                      ON r.id_reunion = n.reunion_id
                                      WHERE n.date_notification='$dateFormat' AND d.libelle_direction='".$rowId['libelle_direction']."'
                                            AND contenu_notification LIKE 'Un compte%'";
                                  }elseif (isset($_GET['directeur'])) {
                                    $requete="SELECT u.nom, u.prenom, u.matricule, u.contact,
                                                      u.mail, u.mise_a_jour, p.libelle_poste,
                                                      d.libelle_direction
                                      FROM utilisateurs AS u
                                    INNER JOIN postes AS p ON
                                    u.poste_id=p.id_poste
                                    INNER JOIN directions AS d ON
                                    p.directions_id=d.id_direction
                                    WHERE u.poste_id=0 AND u.is_deleted = FALSE
                                     ";
                                  }
                       else
                          {
                          $requete="SELECT u.nom, u.prenom, u.matricule, u.contact,
                                            u.mail, u.mise_a_jour, p.libelle_poste,
                                            d.libelle_direction
                            FROM utilisateurs AS u
                          INNER JOIN postes AS p ON
                          u.poste_id=p.id_poste
                          INNER JOIN directions AS d ON
                          p.directions_id=d.id_direction
                          WHERE u.poste_id<>0 AND u.is_deleted = FALSE
                                AND d.libelle_direction='".$rowId['libelle_direction']."'
                           ORDER BY d.libelle_direction, nom";
                        }
                  }
               //...........................Executati0n des requete4...............................
                        $exe_requete= $con->query($requete);
                        if ($exe_requete->rowCount() > 0)
                            {
                              while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC))
                              {

                              $matricule= $row['matricule'];
                              $nom= $row['nom'];
                              $prenom= $row['prenom'];
                              $contact= $row['contact'];
                              $Email= $row['mail'];
                              $poste= $row['libelle_poste'];
                              $direction= $row['libelle_direction'];
                              $mise_a_jour = $row['mise_a_jour'];

                  // Créer un objet DateTime avec le bon fuseau horaire
                  $timezone = new DateTimeZone('Africa/Abidjan'); // Définissez le fuseau horaire approprié ici
                  $miseAjour = new DateTime($mise_a_jour, $timezone);

                  // Définir les paramètres régionaux en français pour la fonction strftime
                  setlocale(LC_TIME, 'fr_FR.UTF-8');

                  // Formater la date en français
                  $formattedDate = strftime('%A %d %B %Y %Hh%M', $miseAjour->getTimestamp());
                              echo"
                                <tr>
                                  <td>".$matricule."</td>
                                  <td>".$nom."</td>
                                  <td>".$prenom."</td>
                                  <td>".$contact."</td>
                                  <td>".$Email."</td>
                                  <td>".$direction."</td>
                                  <td>".$poste."</td>
                                  <td>".$formattedDate."</td>";

                                  if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
                                  <td>
                                    <?php
                                    $id= $matricule;
                                    $url="ajout-utilisateur.php?id=".urlencode($id);
                                    ?>
                                <!--  La cellule de ma colonne action   -->
                                  <div class="d-flex justify-content-between">
                                    <div class="">
                                     <a href="<?php echo $url ?>"><i class="fas fa-pen text-success"></i></a>
                                   </div>
                                     <?php
                                     if (!isset($_GET['directeur'])) {
                                     $url="traitement.php?id_supp=".urlencode($id);
                                     ?>
                                    <div class="">
                                     <a href="<?php echo $url ?>"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"
                                       ><i class="fas fa-trash text-danger"></i></a>
                                   </div>
                                 <?php } ?>
                                 </div>
                            <!-- / La cellule de ma colonne action   -->
                                 </td>
                               <?php }
                              }
                            }else {
                              echo "<center><h4 class='bg-danger'> Aucun utilisateur Enregistré !!! </h4></center>";
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
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
