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
            <input type="search" size="50" name="recherche" value="<?php echo htmlspecialchars($_GET['recherche'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Recherchez par nom, prénom, téléphone, email, poste ou par direction" required>
            <button type="submit" name="bt_rech" class="btn btn-primary">Recherchez</button>
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
              <?php if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
              <a href="ajout-utilisateur.php" class="btn btn-success">
                Ajouter utilisateurs
              </a>
              <?php } ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <!-- Affichage du poste de directeur -->
              <div class="">
                <table id="table" class="table">
                  <?php
                      $requete = "SELECT * FROM utilisateurs AS u
                                  INNER JOIN postes AS p ON u.poste_id = p.id_poste
                                  WHERE u.poste_id = 0 AND u.is_deleted = FALSE";
                      $exe_requete = $con->query($requete);
                      if ($exe_requete->rowCount() > 0) {
                        $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
                        $poste = $row['libelle_poste'];
                        $matriculeDirecteur = $row['matricule'];
                  ?>
                  <tr>
                    <?php
                       $url = "liste-des-utilisateurs.php?directeur=" . urlencode($matriculeDirecteur);
                    ?>
                    <th>
                      <h4 class="text-secondary"><?php echo htmlspecialchars($poste, ENT_QUOTES, 'UTF-8'); ?>:</h4>
                    </th>
                    <th>
                      <h4 class="text-secondary"><?php echo htmlspecialchars($row['nom'] . " " . $row['prenom'], ENT_QUOTES, 'UTF-8'); ?>
                          <?php if ($directeurConnect || $adminRole) { ?>
                        <a href="<?php echo $url ?>" class="text-info">
                          <i class="fa-solid fa-eye"></i>
                        </a>
                      <?php } ?>
                      </h4>
                    </th>
                    <?php if ($directeurConnect || $adminRole) { ?>
                    <th>
                      <?php
                         $url = "ajout-utilisateur.php?id=" . urlencode($matriculeDirecteur);
                      ?>
                      <div class="">
                        <a href="<?php echo $url ?>" class="text-success">Modifier</a>
                      </div>
                    </th>
                  <?php } ?>
                  </tr>
                </table>
              </div>
              <?php } // FIN if ($directeurConnect || $adminRole) ?>

              <table id="table" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>
                      <a href="?sort=matricule<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>">Matricule</a>
                    </th>
                    <th>
                      <a href="?sort=nom<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>">Nom</a>
                    </th>
                    <th>
                      <a href="?sort=prenom<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>">Prénom</a>
                    </th>
                    <th>
                      <a href="?sort=contact<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>">Contact</a>
                    </th>
                    <th>
                      Email
                    </th>
                    <?php
                    if (!isset($_GET['directeur'])) {
                        echo "<th>Sous-direction</th>";
                      } else {
                        echo "<th>Direction</th>";
                      }
                    ?>
                    <th>
                      Poste
                    </th>
                    <th>
                      <a href="?sort=mise_a_jour<?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>">Mise à jour</a>
                    </th>
                    <?php if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
                    <th>
                      <div class="d-flex justify-content-between">
                        <div class="text-success">Modifier</div>
                        <?php if (!isset($_GET['directeur'])) { ?>
                        <div class="text-danger">Supprimer</div>
                        <?php } ?>
                      </div>
                    </th>
                  <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Pagination settings
                  $limit = 5;
                  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                  $offset = ($page - 1) * $limit;

                  // Sorting
                  $sortColumn = isset($_GET['sort']) ? htmlspecialchars($_GET['sort'], ENT_QUOTES, 'UTF-8') : 'nom';
                  $allowedSortColumns = ['matricule', 'nom', 'prenom', 'contact', 'email', 'sous_direction', 'direction', 'poste', 'mise_a_jour'];
                  if (!in_array($sortColumn, $allowedSortColumns)) {
                    $sortColumn = 'nom'; // Default sorting column
                  }

                  // Define the base query
                  $baseQuery = "SELECT u.matricule, u.nom, u.prenom, u.contact, u.mail, u.mise_a_jour, p.libelle_poste, d.libelle_direction
                                FROM utilisateurs AS u
                                INNER JOIN postes AS p ON u.poste_id = p.id_poste
                                INNER JOIN directions AS d ON p.directions_id = d.id_direction
                                WHERE u.is_deleted = FALSE";

                  if ($adminRole || $directeurConnect) {
                    if (isset($_GET['recherche'])) {
                      $champRecherchre = htmlspecialchars($_GET['recherche'], ENT_QUOTES, 'UTF-8');
                      $requete = $baseQuery . " AND (u.nom LIKE '%$champRecherchre%' OR
                                                      u.prenom LIKE '%$champRecherchre%' OR
                                                      u.matricule LIKE '%$champRecherchre%' OR
                                                      d.libelle_direction LIKE '%$champRecherchre%' OR
                                                      u.contact LIKE '%$champRecherchre%' OR
                                                        u.mail LIKE '%$champRecherchre%' OR
                                                      p.libelle_poste LIKE '%$champRecherchre%')
                                ORDER BY $sortColumn
                                LIMIT $limit OFFSET $offset";
                    } elseif (isset($_GET['parametre'])) {
                      $requete = $baseQuery . " INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                                                WHERE n.date_notification = '$dateFormat'
                                                AND contenu_notification LIKE 'Un compte%'
                                                LIMIT $limit OFFSET $offset";
                    } elseif (isset($_GET['directeur'])) {
                      $requete = $baseQuery . " AND u.poste_id = 0
                                                LIMIT $limit OFFSET $offset";
                    } else {
                      $requete = $baseQuery . " AND u.poste_id <> 0
                                                ORDER BY d.libelle_direction, $sortColumn
                                                LIMIT $limit OFFSET $offset";
                    }
                  } else {
                    if (isset($_GET['recherche'])) {
                      $champRecherchre = htmlspecialchars($_GET['recherche'], ENT_QUOTES, 'UTF-8');
                      $requete = $baseQuery . " AND (u.nom LIKE '%$champRecherchre%' OR
                                                      u.prenom LIKE '%$champRecherchre%' OR
                                                      u.matricule LIKE '%$champRecherchre%' OR
                                                      d.libelle_direction LIKE '%$champRecherchre%' OR
                                                      u.contact LIKE '%$champRecherchre%' OR
                                                      u.mail LIKE '%$champRecherchre%' OR
                                                      p.libelle_poste LIKE '%$champRecherchre%')
                                AND d.libelle_direction = '" . htmlspecialchars($rowId['libelle_direction'], ENT_QUOTES, 'UTF-8') . "'
                                ORDER BY $sortColumn
                                LIMIT $limit OFFSET $offset";
                    } elseif (isset($_GET['parametre'])) {
                      $requete = $baseQuery . " INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                                                WHERE n.date_notification = '$dateFormat'
                                                AND d.libelle_direction = '" . htmlspecialchars($rowId['libelle_direction'], ENT_QUOTES, 'UTF-8') . "'
                                                AND contenu_notification LIKE 'Un compte%'
                                                LIMIT $limit OFFSET $offset";
                    } elseif (isset($_GET['directeur'])) {
                      $requete = $baseQuery . " AND u.poste_id = 0
                                                LIMIT $limit OFFSET $offset";
                    } else {
                      $requete = $baseQuery . " AND u.poste_id <> 0
                                                AND d.libelle_direction = '" . htmlspecialchars($rowId['libelle_direction'], ENT_QUOTES, 'UTF-8') . "'
                                                ORDER BY d.libelle_direction, $sortColumn
                                                LIMIT $limit OFFSET $offset";
                    }
                  }

                  $exe_requete = $con->query($requete);
                  if ($exe_requete->rowCount() > 0) {
                    while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                      $matricule = htmlspecialchars($row['matricule'], ENT_QUOTES, 'UTF-8');
                      $nom = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8');
                      $prenom = htmlspecialchars($row['prenom'], ENT_QUOTES, 'UTF-8');
                      $contact = htmlspecialchars($row['contact'], ENT_QUOTES, 'UTF-8');
                      $Email = htmlspecialchars($row['mail'], ENT_QUOTES, 'UTF-8');
                      $poste = htmlspecialchars($row['libelle_poste'], ENT_QUOTES, 'UTF-8');
                      $direction = htmlspecialchars($row['libelle_direction'], ENT_QUOTES, 'UTF-8');
                      $mise_a_jour = htmlspecialchars($row['mise_a_jour'], ENT_QUOTES, 'UTF-8');

                      $timezone = new DateTimeZone('Africa/Abidjan');
                      $miseAjour = new DateTime($mise_a_jour, $timezone);
                      setlocale(LC_TIME, 'fr_FR.UTF-8');
                      $formattedDate = strftime('%A %d %B %Y %Hh%M', $miseAjour->getTimestamp());

                      echo "
                      <tr>
                        <td>$matricule</td>
                        <td>$nom</td>
                        <td>$prenom</td>
                        <td>$contact</td>
                        <td>$Email</td>
                        <td>$direction</td>
                        <td>$poste</td>
                        <td>$formattedDate</td>";

                      if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
                        <td>
                          <?php
                          $url = "ajout-utilisateur.php?id=" . urlencode($matricule);
                          ?>
                          <div class="d-flex justify-content-between">
                            <div class="">
                             <a href="<?php echo $url ?>" class="text-success"><i class="fas fa-pen"></i></a>
                           </div>
                           <?php
                           if (!isset($_GET['directeur'])) {
                           $url = "traitement.php?id_supp=" . urlencode($matricule);
                           ?>
                            <div class="">
                             <a href="<?php echo $url ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" class="text-danger"><i class="fas fa-trash"></i></a>
                           </div>
                         <?php } ?>
                        </div>
                      </td>
                      <?php } ?>
                    </tr>
                  <?php }
                } else {
                  echo "<tr><td colspan='8'>Aucun utilisateur trouvé.</td></tr>";
                }
                  ?>
                </tbody>
              </table>
              <!-- Pagination -->
              <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                  <?php
                  $totalQuery = "SELECT COUNT(*) AS total FROM utilisateurs WHERE is_deleted = FALSE";
                  $totalResult = $con->query($totalQuery)->fetch(PDO::FETCH_ASSOC);
                  $totalRows = $totalResult['total'];
                  $totalPages = ceil($totalRows / $limit);

                  if ($page > 1) {
                    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "&sort=" . urlencode($sortColumn) . "'>&laquo; Précédent</a></li>";
                  }

                  for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($i === $page) ? 'active' : '';
                    echo "<li class='page-item $activeClass'><a class='page-link' href='?page=$i&sort=" . urlencode($sortColumn) . "'>$i</a></li>";
                  }

                  if ($page < $totalPages) {
                    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "&sort=" . urlencode($sortColumn) . "'>Suivant &raquo;</a></li>";
                  }
                  ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
