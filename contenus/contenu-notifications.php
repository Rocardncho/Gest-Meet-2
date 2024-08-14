<div class="container mt-5">
  <?php
  // Requête pour compter le nombre de réunions programmées
  if (!$directeurConnect) {
    $requete = "SELECT COUNT(*) AS total FROM directions d
                INNER JOIN reunions AS r ON d.id_direction = r.directions_id
                INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                WHERE (d.libelle_direction='".$rowId['libelle_direction']."'
                       OR d.id_direction=0) AND
                      n.contenu_notification LIKE 'Une r%' AND
                      r.date_reunion >= '$dateFormat'";
  } else {
    $requete = "SELECT COUNT(*) AS total FROM reunions r
                INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                INNER JOIN directions AS d ON d.id_direction = r.directions_id
                WHERE r.date_reunion >= '$dateFormat' AND
                      n.contenu_notification LIKE 'Une r%'";
  }
  $exe_requete = $con->query($requete);
  $row2 = $exe_requete->fetch(PDO::FETCH_ASSOC);
  if ($row2['total'] > 0) {
    $pas_de_reunion = false;
  ?>
    <!-- Section des Réunions programmées -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Réunions programmées:
              <?php echo $row2['total']; ?>
            </h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table id="table" class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>Notification</th>
                  <th>Date</th>
                  <th>Heure</th>
                  <th>Chargé du compte-rendu</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Requête pour afficher les réunions programmées
                if (!$directeurConnect) {
                  $requete = "SELECT n.contenu_notification, r.date_reunion, r.heure_debut, u.nom, u.prenom
                              FROM notifications AS n
                              INNER JOIN reunions AS r ON n.reunion_id = r.id_reunion
                              INNER JOIN designer AS d ON d.reunion_id = r.id_reunion
                              INNER JOIN utilisateurs AS u ON d.matricule_designer = u.matricule
                              INNER JOIN directions AS di ON di.id_direction = r.directions_id
                              WHERE (di.id_direction = ".$rowId['id_direction']."
                                     OR di.id_direction=0) AND
                                    r.date_reunion >= '$dateFormat' AND
                                    n.contenu_notification LIKE 'Une r%' AND
                                    d.role_joue LIKE 'Charg%'
                              ORDER BY date_notification DESC";
                } else {
                  $requete = "SELECT n.contenu_notification, r.date_reunion, r.heure_debut, u.nom, u.prenom
                              FROM notifications AS n
                              INNER JOIN reunions AS r ON n.reunion_id = r.id_reunion
                              INNER JOIN designer AS d ON d.reunion_id = r.id_reunion
                              INNER JOIN utilisateurs AS u ON d.matricule_designer = u.matricule
                              INNER JOIN directions AS di ON di.id_direction = r.directions_id
                              WHERE r.date_reunion >= '$dateFormat' AND
                                    n.contenu_notification LIKE 'Une r%' AND
                                    d.role_joue = 'Chargé du compte-rendu'
                              ORDER BY date_notification DESC";
                }
                $exe_requete = $con->query($requete);
                while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                  // Formatage de la date et de l'heure en français
                  setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
                  $DateTime = new DateTime($row['date_reunion']);
                  $dateTimeFormate = $DateTime->format('d M Y');
                  $heureFormat = (new DateTime($row['heure_debut']))->format('G \h\e\u\r\e\s i');
                  echo "<tr>
                          <td>{$row['contenu_notification']}</td>
                          <td>{$dateTimeFormate}</td>
                          <td>{$heureFormat}</td>
                          <td>{$row['nom']} {$row['prenom']}</td>
                        </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php
  } else {
    $pas_de_reunion = true;
  }
  // Requête pour compter le nombre de comptes-rendus
  if (!$directeurConnect) {
    $requete = "SELECT COUNT(*) AS total FROM reunions AS r
                INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                INNER JOIN directions AS d ON d.id_direction = r.directions_id
                WHERE (d.libelle_direction='".$rowId['libelle_direction']."'
                       OR d.id_direction=0) AND
                      r.compte_rendu IS NOT NULL AND
                      n.contenu_notification LIKE 'Un compte-rendu%'";
  } else {
    $requete = "SELECT COUNT(*) AS total FROM reunions AS r
                INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                INNER JOIN directions AS d ON d.id_direction = r.directions_id
                WHERE r.compte_rendu IS NOT NULL AND
                      n.contenu_notification LIKE 'Un compte-rendu%'";
  }
  $exe_requete = $con->query($requete);
  $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
  if ($row['total'] > 0) {
    $pas_de_compte_rendu = false;
  ?>
    <!-- Section des Compte-rendus disponibles -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Compte-rendus disponibles:
              <?php echo $row['total']; ?>
            </h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table id="table" class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>Notification</th>
                  <th>Date</th>
                  <th>Heure</th>
                  <th>Compte-rendu</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Requête pour afficher les notifications compte-rendu
                if (!$directeurConnect) {
                  $requete = "SELECT * FROM notifications AS n
                              INNER JOIN reunions AS r ON n.reunion_id = r.id_reunion
                              INNER JOIN directions AS d ON d.id_direction = r.directions_id
                              WHERE (d.libelle_direction='".$rowId['libelle_direction']."'
                                     OR d.id_direction=0) AND
                                    r.compte_rendu IS NOT NULL AND
                                    n.contenu_notification LIKE 'Un compte%'
                              ORDER BY r.date_reunion DESC";
                } else {
                  $requete = "SELECT * FROM notifications AS n
                              INNER JOIN reunions AS r ON n.reunion_id = r.id_reunion
                              INNER JOIN directions AS d ON d.id_direction = r.directions_id
                              WHERE r.compte_rendu IS NOT NULL AND
                                    n.contenu_notification LIKE 'Un compte%'
                              ORDER BY r.date_reunion DESC";
                }
                $exe_requete = $con->query($requete);
                while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                  // Formatage de la date et de l'heure en français
                  setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
                  $DateTime = new DateTime($row['date_reunion']);
                  $dateTimeFormate = $DateTime->format('d M Y');
                  $heureFormat = (new DateTime($row['heure_debut']))->format('G \h\e\u\r\e\s i');
                  echo "<tr>
                          <td>{$row['contenu_notification']}</td>
                          <td>{$dateTimeFormate}</td>
                          <td>{$heureFormat}</td>
                          <td><a href='{$row['compte_rendu']}'>Télécharger</a></td>
                        </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php
  } else {
    $pas_de_compte_rendu = true;
  }
  if ($pas_de_compte_rendu && $pas_de_reunion) {
    echo "<h2 class='bg-info'><center>Vous n'avez pas de notification pour l'instant!!!</center></h2>
          <center>
            <a href='javascript:history.go(-1)' class='bg-secondary'>Retour</a>
          </center>";
  }
  ?>
</div>
