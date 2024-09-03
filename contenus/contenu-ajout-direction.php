<div class="card-header">
    <h3 class="card-title">Ajouter des sous-directions</h3>
    <div class="card-tools">
        <span title="3 New Messages" class="badge badge-primary"></span>
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
        <div class="">
            <a href="#div1">Voir la liste des sous-directions</a>
        </div>
        <div class="">
            <a href="ajout-poste.php">Ajouter un poste</a>
        </div>
        <div class="">
            <a href="ajout-utilisateur.php">Ajouter un utilisateur</a>
        </div>
    </div>
    <!-- /lien horizontal -->
    <!-- Conversations are loaded here -->
    <div class="direct-chat-messages">
        <!-- Message. Default to the left -->
        <div class="direct-chat-msg">
            <div class="direct-chat-infos clearfix">
                <form style="background-color:#bbbbbb;padding:15px" class="formulaires" action="traitement.php" method="post"
                <?php if (!isset($_GET['modDirection'])) { ?>
                    onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cette sous-direction ?')"
                <?php } else { ?>
                    onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cette sous-direction ?')"
                <?php } ?>
                >
                    <?php
                    // En cas de modification de la direction
                    if (isset($_GET['modDirection'])) {
                        $idDirection = $_GET['modDirection'];
                        // Requête si le paramètre modify est envoyé dans cette adresse
                        $requete = "SELECT * FROM directions WHERE id_direction='$idDirection' ";
                        $exe_requete = $con->query($requete);
                        $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>
                    <table id="table">
                        <tr>
                            <?php if (isset($idDirection) AND $idDirection == 0) {
                                echo "<td> <label for=''>-- Direction:</label> </td>";
                            } else { ?>
                                <td><label for="">-- Sous-direction:</label></td>
                            <?php } ?>
                            <td><input type="text" value="<?php if (isset($idDirection)) { echo $row['libelle_direction']; } ?>" name="direction" size="80" required></td>
                        </tr>
                    </table><br><br>
                    <center>
                        <?php if (isset($idDirection)) { ?>
                            <input type="hidden" name="idDirection" value="<?php echo $idDirection; ?>">
                            <!-- Bouton de soumission -->
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <input class="bg-success" type="submit" name="bt_modif_direct" value="Modifier">
                                </div>
                                <div class="">
                                    <a href='javascript:history.go(-1)' class='bg-secondary'>Annuler la modification</a>
                                </div>
                            </div>
                            <!-- /Bouton de soumission -->
                        <?php } else { ?>
                            <!-- Bouton de soumission -->
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <input class="bg-success" type="submit" name="bt_ajout_direct" value="Ajouter">
                                </div>
                                <div class="">
                                    <input class="bg-danger" type="reset" name="bt_annul" value="Annuler">
                                </div>
                            </div>
                            <!-- /Bouton de soumission -->
                        <?php } ?>
                    </center>
                </form>
            </div>
            <!-- /direct-chat-infos clearfix -->
        </div>
        <!-- /.direct-chat-msg -->
    </div>
    <!-- /.direct-chat-message -->
    <!-- ************ Affichage de la direction ************ -->
    <div id="div1" class="">
        <table id="table">
            <?php
            $requete = "SELECT * FROM directions WHERE id_direction=0 AND is_deleted=FALSE";
            $exe_requete = $con->query($requete);
            if ($exe_requete->rowCount() > 0) {
                $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
                $direction = $row['libelle_direction'];
                $idDirection = $row['id_direction'];
            ?>
            <tr>
                <th><h4 style="color:#0000cc;"><?php echo $direction ?></h4></th>
                <th>
                <?php
                $url = "ajout-direction.php?modDirection=" . urlencode($idDirection);
                ?>
                    <div class="">
                        <a href="<?php echo $url ?>"><i class="fas fa-pen text-success">Modifier la direction</i></a>
                    </div>
            </tr>
            <?php } ?>
        </table>
    </div>
    <!-- /.card-body -->
    <!-- ************ Affichage de la liste des sous-directions avec pagination ************ -->
    <div class="">
        <h3>&nbsp;&nbsp;&nbsp;&nbsp;Liste des sous-directions</h3>
    </div>
    <?php
    // Pagination setup
    $limit = 5; // Nombre de lignes par page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Requête pour compter le nombre total d'enregistrements
    $count_query = "SELECT COUNT(*) AS total FROM directions WHERE id_direction<>0 AND is_deleted=FALSE";
    $count_result = $con->query($count_query);
    $total = $count_result->fetch(PDO::FETCH_ASSOC)['total'];
    $pages = ceil($total / $limit);

    // Requête pour récupérer les sous-directions avec la limite de pagination
    $requete = "SELECT * FROM directions WHERE id_direction<>0 AND is_deleted=FALSE ORDER BY libelle_direction LIMIT $start, $limit";
    $exe_requete = $con->query($requete);
    ?>
    <div class="card-body">
        <table id="table" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Sous-directions</th>
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
                $idDirection = $row['id_direction'];
                $mise_a_jour = $row['mise_a_jour'];
                // Créer un objet DateTime
                $timezone = new DateTimeZone('Africa/Abidjan');
                $miseAjour = new DateTime($mise_a_jour, $timezone);
                // Définir les paramètres régionaux en français
                setlocale(LC_TIME, 'fr_FR.UTF-8');
                // Formater la date en français
                $formattedDate = strftime('%A %d %B %Y %Hh%M', $miseAjour->getTimestamp());
            ?>
            <tr>
                <th style="color:#0000cc"><?php echo $direction ?></th>
                <td><?php echo $formattedDate; ?></td>
                <th>
                <?php
                $url = "ajout-direction.php?modDirection=" . urlencode($idDirection);
                ?>
                <div class="d-flex justify-content-between">
                    <div class="">
                        <a href="<?php echo $url ?>"><i class="fas fa-pen text-success"></i></a>
                    </div>
                    <?php
                    $url = "traitement.php?supDirection=" . urlencode($idDirection);
                    ?>
                    <div class="">
                        <a href="<?php echo $url ?>"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette sous-direction ?');">
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
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1) { ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Précédent</a>
                </li>
            <?php } ?>
            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
            <?php if ($page < $pages) { ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Suivant</a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>
