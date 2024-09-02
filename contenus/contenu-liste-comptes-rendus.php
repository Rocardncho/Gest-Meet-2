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
            <input type="search" size="40" name="recherche" value="<?php if (isset($_GET['recherche'])) {
              echo $_GET['recherche'];
            } ?>" placeholder="Recherchez par objet ou par date(EX: 2024-06-25)" required>
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
              // Sélection du matricule de $username
              $sql = "SELECT matricule FROM utilisateurs WHERE mail='$username' AND is_deleted=FALSE";
              $exe = $con->query($sql);
              $row = $exe->fetch(PDO::FETCH_ASSOC);
              $matricule = $row['matricule'];

              // Gestion du tri
              $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_reunion';
              $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';

              // Pagination
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $per_page = 5; // Nombre de lignes par page
              $offset = ($page - 1) * $per_page;

              // Requête en fonction du tri, de la recherche et de la pagination
              if ($directeurConnect) {
                if (isset($_GET['recherche'])) {
                  $champRecherchre = $_GET['recherche'];
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              WHERE (date_reunion LIKE '%$champRecherchre%' OR
                                     r.ordre_du_jour LIKE '%$champRecherchre%')
                                AND r.compte_rendu IS NOT NULL
                                AND d.role_joue='Chargé du compte-rendu'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                } elseif (isset($_GET['parametre'])) {
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                              WHERE n.date_notification BETWEEN DATE_SUB('$dateFormat', INTERVAL 6 DAY) AND '$dateFormat'
                                AND r.compte_rendu IS NOT NULL
                                AND d.role_joue='Chargé du compte-rendu'
                                AND contenu_notification LIKE 'Un compte%'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                } else {
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              WHERE r.compte_rendu IS NOT NULL
                                AND d.role_joue='Chargé du compte-rendu'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                }
              } else {
                if (isset($_GET['recherche'])) {
                  $champRecherchre = $_GET['recherche'];
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              WHERE (date_reunion LIKE '%$champRecherchre%' OR
                                     r.ordre_du_jour LIKE '%$champRecherchre%')
                                AND (di.id_direction=" . $rowId['id_direction'] . " OR di.id_direction=0)
                                AND r.compte_rendu IS NOT NULL
                                AND d.role_joue='Chargé du compte-rendu'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                } elseif (isset($_GET['parametre'])) {
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                              WHERE n.date_notification BETWEEN DATE_SUB('$dateFormat', INTERVAL 6 DAY) AND '$dateFormat'
                                AND r.compte_rendu IS NOT NULL
                                AND (di.id_direction=" . $rowId['id_direction'] . " OR di.id_direction=0)
                                AND d.role_joue='Chargé du compte-rendu'
                                AND contenu_notification LIKE 'Un compte%'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                } else {
                  $requete = "SELECT * FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              WHERE r.compte_rendu IS NOT NULL
                                AND (di.id_direction=" . $rowId['id_direction'] . " OR di.id_direction=0)
                                AND d.role_joue='Chargé du compte-rendu'
                                ORDER BY $sort_by $sort_order
                                LIMIT $per_page OFFSET $offset";
                }
              }

              $exe_requete = $con->query($requete);

              // Compter le nombre total de résultats pour la pagination
              $count_query = "SELECT COUNT(*) FROM directions AS di
                              INNER JOIN reunions AS r ON di.id_direction=r.directions_id
                              INNER JOIN designer AS d ON r.id_reunion = d.reunion_id
                              WHERE r.compte_rendu IS NOT NULL
                                AND d.role_joue='Chargé du compte-rendu'";
              $count_result = $con->query($count_query);
              $total_rows = $count_result->fetchColumn();
              $total_pages = ceil($total_rows / $per_page);
              ?>

              <table id="table" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th><a href="liste-des-comptes-rendus.php?sort_by=libelle_direction&sort_order=<?= $sort_order === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>">Directions concernées</a></th>
                    <th><a href="liste-des-comptes-rendus.php?sort_by=date_reunion&sort_order=<?= $sort_order === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>">Date de la reunion</a></th>
                    <th><a href="liste-des-comptes-rendus.php?sort_by=heure_debut&sort_order=<?= $sort_order === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>">Heure de la reunion</a></th>
                    <th><a href="liste-des-comptes-rendus.php?sort_by=ordre_du_jour&sort_order=<?= $sort_order === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>">Ordre du jour</a></th>
                    <th>Compte rendu</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                if ($exe_requete->rowCount() > 0) {
                  while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                    // Formatage de la date
                    setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
                    $dateReunion = $row['date_reunion'];
                    $DateTime = new DateTime($dateReunion);
                    $jourSemaine = utf8_encode(strftime('%A', $DateTime->getTimestamp()));
                    $jour = $DateTime->format('d');
                    $mois = utf8_encode(strftime('%B', $DateTime->getTimestamp()));
                    $annee = $DateTime->format('Y');

                    // Formatage de l'heure
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
                        <td><a href=\"{$compte}\">Télécharger le Compte-rendu</a>";
                    // En cas de modification ou suppression
                    if ($row['matricule_designer'] == $matricule) {
                      $urlMod = "ajout-de-comptes-rendus.php?modCompte=" . urlencode($idReunion);
                      $urlSup = "traitement.php?supCompte=" . urlencode($idReunion);
                      echo "&nbsp;&nbsp;&nbsp;<a href='{$urlMod}'><i class='fas fa-pen text-success'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo "<a href='{$urlSup}' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce compte-rendu ?');\"><i class='fas fa-trash text-danger'></i></a>";
                    }
                    echo "</td></tr>";
                  }
                } else {
                  echo "<center><h4 class='text-danger'> Aucun compte-rendu disponible </h4></center>";
                }
                ?>
                </tbody>
              </table>

              <!-- Pagination -->
              <div class="pagination">
                <?php if ($page > 1): ?>
                  <a href="?page=<?= $page - 1 ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Précédent</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                  <a href="?page=<?= $i ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" <?= ($i == $page) ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                  <a href="?page=<?= $page + 1 ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Suivant</a>
                <?php endif; ?>
              </div>

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
    <br><br>
    <center>
      <a href='javascript:history.go(-1)' class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="reunions.php"><button type="button" name="button">Toutes les réunions</button></a>
    </center>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<style>
.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.pagination a {
  padding: 8px 16px;
  margin: 0 4px;
  border: 1px solid #ddd;
  text-decoration: none;
  color: #007bff;
}

.pagination a.active {
  background-color: #007bff;
  color: white;
  border: 1px solid #007bff;
}

.pagination a:hover {
  background-color: #ddd;
}
</style>
