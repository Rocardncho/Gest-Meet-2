<div class="card-header">
    <h3 class="card-title">Ajouter des postes</h3>
    <div class="card-tools">
        <span title="3 New Messages" class="badge badge-primary">Défilez jusqu'en bas avec la souris</span>
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<!-- /.card-header -->

<div class="card-body">
    <!-- lien horizontal -->
    <div class="d-flex justify-content-between">
        <div>
            <a href="#div1">Voir la liste des postes</a>
        </div>
        <?php if (in_array(11, $roles) || in_array(12, $roles)) : ?>
        <div>
            <?php if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
            <a href="ajout-utilisateur.php">Ajouter un utilisateur</a>
            <?php } ?>
        </div>
        <?php endif;
        if ($directeurConnect || $adminRole) : ?>
        <div>
            <a href="ajout-direction.php">Ajouter une sous-direction</a>
        </div>
        <?php endif; ?>
    </div>
    <!-- /lien horizontal -->

    <!-- Formulaire de poste -->
    <div class="direct-chat-messages">
        <div class="direct-chat-msg">
            <div class="direct-chat-infos clearfix">
                <form style="background-color:#bbbbbb;padding:15px" class="formulaires" action="traitement.php" method="post"
                <?php if (!isset($_GET['modPoste'])) { ?>
                    onsubmit="return confirm('Êtes-vous sûr de ajouter ce poste ?')"
                <?php } else { ?>
                    onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce poste ?')">
                <?php } ?>
                >
                    <?php
                    if (isset($_GET['modPoste'])) {
                        $idPoste = $_GET['modPoste'];
                        $requete = "SELECT * FROM postes AS p
                                    INNER JOIN directions AS d ON p.directions_id=d.id_direction
                                    WHERE id_poste='$idPoste'";
                        $exe_requete = $con->query($requete);
                        $row2 = $exe_requete->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>
                    <table id="table">
                        <tr>
                            <?php if (isset($idPoste) && $idPoste == 0) {
                                echo "<td> <label for=''>--direction: &nbsp;&nbsp;</label> </td>";
                            } else { ?>
                                <td> <label for="">-sous-direction: &nbsp;&nbsp;</label> </td>
                            <?php } ?>
                            <td>
                                <select class="" name="select_direct" required>
                                    <option value="" disabled <?php if (!isset($idPoste)) echo "selected"; ?>>---Sélectionner la direction---</option>
                                    <?php
                                    if (isset($idPoste) && $idPoste == 0) {
                                        $requete = "SELECT * FROM directions WHERE id_direction=0 AND is_deleted=FALSE";
                                    } else {
                                        if ($directeurConnect || $adminRole) {
                                            $requete = "SELECT * FROM directions WHERE id_direction<>0 AND is_deleted=FALSE";
                                        } else {
                                            $requete = "SELECT * FROM directions WHERE id_direction<>0 AND libelle_direction='".$rowId['libelle_direction']."'
                                                        AND is_deleted=FALSE";
                                        }
                                    }
                                    $exe_requete = $con->query($requete);
                                    if ($exe_requete->rowCount() > 0) {
                                        while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='".$row['id_direction']."'";
                                            if (isset($idPoste) && $row['id_direction'] === $row2['id_direction']) {
                                                echo "selected";
                                            }
                                            echo ">".$row['libelle_direction']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td> <label for="">-- Poste:</label> </td>
                            <td> <input type="text" value="<?php if (isset($idPoste)) { echo $row2['libelle_poste']; } ?>" name="poste" size="35" required> </td>
                        </tr>
                    </table>
                    <br><br>
                    <center>
                        <?php if (isset($idPoste)) { ?>
                            <input type="hidden" class="form-control" name="idPoste" value="<?php echo $idPoste; ?>">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <input class="bg-success" type="submit" name="bt_modif_poste" value="Modifier">&nbsp;&nbsp;
                                </div>
                                <div>
                                    <a href='javascript:history.go(-1)' class='bg-secondary'>Annuler la modification</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <input class="bg-success" type="submit" name="bt_ajout_post" value="Ajouter">
                                </div>
                                <div>
                                    <input class="bg-danger" type="reset" name="bt_annul" value="Annuler">
                                </div>
                            </div>
                        <?php } ?>
                    </center>
                </form>
            </div>
        </div>
    </div>

    <div id="div1" class="">
        <h3>&nbsp;&nbsp;&nbsp;&nbsp;Liste des postes</h3>
    </div>
    <!--************ Affichage du poste de directeur******************* -->
    <?php if ($directeurConnect || $adminRole) { ?>
    <div class="">
     <table id="table">
       <?php
           $requete="SELECT *  FROM postes WHERE id_poste=0 AND is_deleted=FALSE";
           $exe_requete= $con->query($requete);
          if ($exe_requete->rowCount()>0) {
            // code...
              $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
              $poste= $row['libelle_poste'];
              $idPoste= $row['id_poste'];
        ?>
       <tr>
           <th><h4 style="color:#0000cc;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $poste ?></h4></th>
           <th>
           <?php
              $url="ajout-poste.php?modPoste=".urlencode($idPoste);
           ?>
             <div class="">
                &nbsp;&nbsp;<a href="<?php echo $url ?>"><i class="fas fa-pen text-success">Modifier le poste   << <?php echo $poste ?>>></i></a>
             </div>
           </th>
       </tr>
     <?php } ?>
     </table>
    </div>
    <?php
    }  //FIN if ($directeurConnect || $adminRole) ?>
    <!-- Options de tri -->
    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="?sort=direction" class="btn btn-secondary">Trier par Sous-direction</a>
            <a href="?sort=poste" class="btn btn-secondary">Trier par Poste</a>
        </div>
    </div>
    <!-- Affichage de la liste des postes -->
    <?php
    $limit = 5; // Nombre de lignes par page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Déterminer le tri
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'direction';
    $orderBy = 'd.libelle_direction'; // Valeur par défaut
    if ($sort === 'poste') {
        $orderBy = 'p.libelle_poste';
    }

    // Requête avec tri et pagination
    if ($directeurConnect || $adminRole) {
        $requete = "SELECT p.libelle_poste, p.id_poste,
                    d.libelle_direction, d.id_direction,
                    p.mise_a_jour
                    FROM directions AS d
                    INNER JOIN postes AS p ON d.id_direction=p.directions_id
                    WHERE p.id_poste<>0 AND p.is_deleted=FALSE
                    ORDER BY $orderBy
                    LIMIT $limit OFFSET $offset";
    } else {
        $requete = "SELECT p.libelle_poste, p.id_poste,
                    d.libelle_direction, d.id_direction,
                    p.mise_a_jour
                    FROM directions AS d
                    INNER JOIN postes AS p ON d.id_direction=p.directions_id
                    WHERE p.id_poste<>0 AND d.libelle_direction='".$rowId['libelle_direction']."'
                    AND p.is_deleted=FALSE
                    ORDER BY $orderBy
                    LIMIT $limit OFFSET $offset";
    }
    $exe_requete = $con->query($requete);
    ?>

    <div class="card-body">
        <table id="table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <td>Sous-directions</td>
                    <th>Postes</th>
                    <td>Mise à jour</td>
                    <td>
                        <div class="d-flex justify-content-between">
                            <div class="text-success">Modifier</div>
                            <div class="text-danger">Supprimer</div>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                    $direction = $row['libelle_direction'];
                    $poste = $row['libelle_poste'];
                    $idDirection = $row['id_direction'];
                    $idPoste = $row['id_poste'];
                    $mise_a_jour = $row['mise_a_jour'];
                    $timezone = new DateTimeZone('Africa/Abidjan');
                    $miseAjour = new DateTime($mise_a_jour, $timezone);
                    setlocale(LC_TIME, 'fr_FR.UTF-8');
                    $formattedDate = strftime('%A %d %B %Y %Hh%M', $miseAjour->getTimestamp());
                ?>
                <tr>
                    <td><?php echo $direction ?></td>
                    <th style="color:#0000cc"><?php echo $poste ?></th>
                    <td><?php echo $formattedDate ?></td>
                    <th>
                        <?php
                        $url = "ajout-poste.php?modPoste=" . urlencode($idPoste);
                        ?>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?php echo $url ?>"><i class="fas fa-pen text-success"></i></a>
                            </div>
                            <?php
                            $url = "traitement.php?supPoste=" . urlencode($idPoste);
                            ?>
                            <div>
                                <a href="<?php echo $url ?>"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce poste ?');">
                                    <i class="fas fa-trash text-danger"></i></a>
                            </div>
                        </div>
                    </th>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php
    // Compter le nombre total de postes
    $countQuery = "SELECT COUNT(*) AS total FROM postes AS p
                   INNER JOIN directions AS d ON d.id_direction=p.directions_id
                   WHERE p.id_poste<>0 AND p.is_deleted=FALSE";
    $countResult = $con->query($countQuery);
    $totalRows = $countResult->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRows / $limit);
    ?>

    <nav>
        <ul class="pagination">
            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>&sort=<?php echo $sort; ?>" aria-label="Précédent">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>&sort=<?php echo $sort; ?>" aria-label="Suivant">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<!-- /.card-body -->
