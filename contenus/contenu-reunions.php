<!-- Content Wrapper. Contains page content -->
<div class="content">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Liste des reunions <?php if (isset($_GET['parametre'])) echo "programmées"; ?></h1>
        </div>
        <div class="col-sm-6">
        <form class="" action="" method="get">
            <input type="search" size="50" name="recherche" value="<?php if (isset($_GET['recherche'])) {
              echo $_GET['recherche'];
            } ?>" placeholder="Recherchez par date ou par objret ou par date(EX: 2024-09-13)" required>
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
              <!-- lien horizontal-->
              <div class="d-flex justify-content-between">
                <?php if (in_array(5, $roles)) : ?>
                  <div class="">
                     <a href="programmer-reunion.php">Programmer une reunion</a>
                 </div>
               <?php endif; ?>
                     <div class="">
                        <a href="liste-des-comptes-rendus.php">Voir la liste des comptes-rendus</a>
                    </div>
                    <?php if (in_array(3, $roles)) : ?>
                    <div class=" ">
                       <a href="ajout-de-comptes-rendus.php">Ajouter un compte-rendu</a>
                    </div>
                  <?php endif; ?>
                </div>
              <!-- /lien horizontal-->
              <table id="table" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Date de la reunion</th>
                  <th>Heure de la reunion</th>
                  <th>Ordre du jour</th>
                  <th>Heure de fin</th>
                  <th>Chargé du compte-rendu</th>
                  <?php
                    if ($progReunRole) {
                  ?>
                  <th>
                    <div class="d-flex justify-content-between">
                     <div class="text-success">Modifier</div>
                     <div class="text-danger">Supprimer</div>
                   </div>
                 </th>
               <?php } ?>
                </tr>
                </thead>
                <tbody>
<?php
$date= new DateTime();
$dateFormat=$date->format('Y-m-d');//date d'aujourd'hui
$dateFormat = date('Y-m-d'); // Assurez-vous que $dateFormat est défini
if (!$directeurConnect) {
    $requete = "SELECT *
                FROM directions AS d
                INNER JOIN reunions AS r ON r.directions_id = d.id_direction
                INNER JOIN designer AS dr ON r.id_reunion = dr.reunion_id
                INNER JOIN utilisateurs AS u ON u.matricule = dr.matricule_designer";
    if (isset($_GET['recherche'])) {
        $champRecherchre = $_GET['recherche'];
        $requete .= " WHERE (d.libelle_direction = :libelle_direction
                        OR d.id_direction=0) AND r.is_deleted = FALSE
                      AND (r.date_reunion LIKE :champRecherchre OR r.ordre_du_jour LIKE :champRecherchre)
                      ORDER BY r.date_reunion DESC";
        $params = [
            ':libelle_direction' => $rowId['libelle_direction'],
            ':champRecherchre' => "%$champRecherchre%"
        ];
    } elseif (isset($_GET['parametre'])) {
        $requete .= " WHERE (d.libelle_direction = :libelle_direction
                        OR d.id_direction=0) AND r.date_reunion >= :dateFormat
                        AND role_joue ='Chargé du compte-rendu'
                        AND r.is_deleted = FALSE
                        ORDER BY r.date_reunion";
        $params = [
            ':libelle_direction' => $rowId['libelle_direction'],
            ':dateFormat' => $dateFormat
        ];
    } elseif (isset($_GET['para'])) {
        $requete .= " WHERE (d.libelle_direction = :libelle_direction
                            OR d.id_direction=0) AND r.date_reunion < :dateFormat
                      AND r.compte_rendu IS NULL AND r.is_deleted = FALSE
                      AND dr.role_joue = 'Chargé du compte-rendu'";
        $params = [
            ':libelle_direction' => $rowId['libelle_direction'],
            ':dateFormat' => $dateFormat
        ];
    } elseif (isset($_GET['parametre2'])) {
        $requete .= " INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                      WHERE (d.libelle_direction = :libelle_direction
                            OR d.id_direction=0) AND :dateFormat <= r.date_reunion
                      AND n.contenu_notification LIKE 'Une reunion%' AND n.is_deleted = FALSE
                      AND role_joue='Chargé du compte-rendu'
                      ORDER BY r.date_reunion";
        $params = [
            ':libelle_direction' => $rowId['libelle_direction'],
            ':dateFormat' => $dateFormat
        ];
    } else {
        $requete .= " WHERE (d.libelle_direction = :libelle_direction
                        OR d.id_direction=0) AND r.is_deleted = FALSE
                        AND dr.role_joue = 'Chargé du compte-rendu'
                        ORDER BY r.date_reunion DESC";
        $params = [
            ':libelle_direction' => $rowId['libelle_direction']
        ];
    }
} else { // Cas du DIRECTEUR
    $requete = "SELECT *
                FROM directions AS d
                INNER JOIN reunions AS r ON r.directions_id = d.id_direction
                INNER JOIN designer AS dr ON r.id_reunion = dr.reunion_id
                INNER JOIN utilisateurs AS u ON u.matricule = dr.matricule_designer";
    if (isset($_GET['recherche'])) {
        $champRecherchre = $_GET['recherche'];
        $requete .= " WHERE r.is_deleted = FALSE AND (r.date_reunion LIKE :champRecherchre OR r.ordre_du_jour LIKE :champRecherchre)";
        $params = [
            ':champRecherchre' => "%$champRecherchre%"
        ];
    } elseif (isset($_GET['parametre'])) {
        $requete .= " WHERE r.date_reunion >= :dateFormat
        AND r.is_deleted = FALSE
        AND dr.role_joue ='Chargé du compte-rendu'
        ORDER BY r.date_reunion";
        $params = [
            ':dateFormat' => $dateFormat
        ];
    } elseif (isset($_GET['para'])) {
        $requete .= " WHERE r.date_reunion < :dateFormat AND r.compte_rendu IS NULL
                      AND r.is_deleted = FALSE
                      AND dr.role_joue = 'Chargé du compte-rendu'";
        $params = [
            ':dateFormat' => $dateFormat
        ];
    } elseif (isset($_GET['parametre2'])) {
        $requete .= " INNER JOIN notifications AS n ON r.id_reunion = n.reunion_id
                      WHERE :dateFormat <= r.date_reunion AND n.contenu_notification LIKE 'Une reunion%'
                      AND n.is_deleted = FALSE AND dr.role_joue='Chargé du compte-rendu'
                      ORDER BY r.date_reunion";
        $params = [
            ':dateFormat' => $dateFormat
        ];
    } else {
        $requete .= " WHERE r.is_deleted = FALSE
              AND dr.role_joue = 'Chargé du compte-rendu'
        ORDER BY r.date_reunion DESC";
        $params = [];
    }
}

$stmt = $con->prepare($requete);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($row['libelle_direction'])) {
            echo "Clé 'libelle_direction' introuvable dans \$row<br>";
        } else {
            $direction = $row['libelle_direction'];
            setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
            $dateReunion = $row['date_reunion'];
            $DateTime = new DateTime($dateReunion);
            $jourSemaine = utf8_encode(strftime('%A', $DateTime->getTimestamp()));
            $jour = $DateTime->format('d');
            $mois = utf8_encode(strftime('%B', $DateTime->getTimestamp()));
            $annee = $DateTime->format('Y');
            $heureDebut = $row['heure_debut'];
            $heureDebutTime = new DateTime($heureDebut);
            $heureDebutFormat = $heureDebutTime->format('G \H i');
            $heureFin = $row['heure_fin'];
            $heureFinTime = new DateTime($heureFin);
            $heureFinFormat = $heureFinTime->format('G \H i');
            $ordre = $row['ordre_du_jour'];
            $idReunion = $row['id_reunion'];

            echo "
                <tr>
                    <td>" . htmlspecialchars($direction) . "</td>
                    <td>" . htmlspecialchars($jourSemaine) . " " . htmlspecialchars($jour) . " " . htmlspecialchars($mois) . " " . htmlspecialchars($annee) . "</td>
                    <td>" . htmlspecialchars($heureDebutFormat) . "</td>
                    <td>" . htmlspecialchars($ordre) . "</td>
                    <td>" . htmlspecialchars($heureFinFormat) . "&nbsp;&nbsp;";
            if ($dateReunion < $dateFormat) {
                echo "<i class='fas fa-check text-success'>Terminee</i>";
            }
            echo "</td>
                   <th>" . htmlspecialchars($row['nom']) . " " . htmlspecialchars($row['prenom']) . "</th>";
            if ($progReunRole && $dateReunion >= $dateFormat) {
                $requete = "SELECT u.matricule
                            FROM utilisateurs AS u
                            INNER JOIN reunions AS r ON r.programmer_par = u.matricule
                            WHERE u.mail = :username AND u.is_deleted = FALSE";
                $stmtProg = $con->prepare($requete);
                $stmtProg->bindParam(':username', $username, PDO::PARAM_STR);
                $stmtProg->execute();
                if ($stmtProg->rowCount() > 0) {
                    $prog = $stmtProg->fetch(PDO::FETCH_ASSOC);
                    if ($prog['matricule'] == $row['programmer_par']) {
                        $urlMod = "programmer-reunion.php?modReunion=" . urlencode($idReunion);
                        $urlSup = "traitement.php?supReunion=" . urlencode($idReunion);
                        echo "<th>
                                <div class='d-flex justify-content-between'>
                                    <div class=''><a href='$urlMod'><i class='fas fa-pen text-success'></i></a></div>
                                    <div class=''><a href='$urlSup' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette réunion ?');\"><i class='fas fa-trash text-danger'></i></a></div>
                                </div>
                              </th>";
                    }
                }
            }
            echo "</tr>";
        }
    }
} else {
    echo "<center><h4 class='text-danger'>Aucune reunion programmées</h4></center>";
}
?>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <br><br>  <center>
                                <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="reunions.php"><button type="button" name="button">Toutes les réunions</button></a>
                      </center>
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
