<div class="card-header">
    <h3 class="card-title">Ajouter des utilisateurs</h3>
    <div class="card-tools">
        <span title="3 New Messages" class="badge badge-primary">Défilez jusqu'en bar avec la souris</span>
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
    <!-- lien horizontal-->
    <div class="d-flex justify-content-between">
        <div class="">
            <a href="liste-des-utilisateurs.php">Voir la liste des utilisateurs</a>
        </div>
        <div class=" ">
            <a href="ajout-poste.php">Ajouter un poste</a>
        </div>
        <div class="">
            <a href="ajout-direction.php">Ajouter une direction</a>
        </div>
    </div>
    <!-- /lien horizontal -->

    <!-- Formulaires -->
    <div class="direct-chat-messages">
        <div class="direct-chat-msg">
            <div class="direct-chat-infos clearfix">
                <?php
                try {
                    $matriculeDirecteur = '';
                    $id = isset($_GET['id']) ? $_GET['id'] : '';

                    // Récupérer les informations du directeur
                    $requete = "SELECT * FROM utilisateurs AS u
                                INNER JOIN postes AS p ON u.poste_id = p.id_poste
                                INNER JOIN directions AS d ON d.id_direction = p.directions_id
                                WHERE u.poste_id = 0 AND u.is_deleted = FALSE";
                    $exe_requete = $con->query($requete);
                    if ($exe_requete->rowCount() > 0) {
                        $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
                        $matriculeDirecteur = $row['matricule'];
                    }

                    // Récupérer les informations de l'utilisateur en fonction du matricule passé dans l'URL
                    $stmt = $con->prepare("
                        SELECT * FROM utilisateurs AS u
                        INNER JOIN postes AS p ON u.poste_id = p.id_poste
                        INNER JOIN directions AS d ON p.directions_id = d.id_direction
                        WHERE matricule = ?");
                    $stmt->execute([$id]);
                    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!empty($id) && $id == $matriculeDirecteur) {
                        $row1 = $row;
                    }

                    if ($row1) {
                        $selectedDirectionID = $row1['id_direction'];
                        $selectedPosteID = $row1['id_poste'];
                    } else {
                        $selectedDirectionID = '';
                        $selectedPosteID = '';
                    }
                } catch (PDOException $e) {
                    echo "Erreur: " . $e->getMessage();
                }
                ?>
                <form style="background-color: #bbbbbb; padding: 15px;" class="formulaires" action="traitement.php" method="post"
                <?php if (empty($id)) { ?>
                  onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet utilisateur ?')"
                <?php }else { ?>
                  onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cet utilisateur ?')"
                <?php } ?>
                >
                    <?php
                    if (!empty($id)) {
                        // Gestion des rôles
                        $requete = "SELECT * FROM utilisateurs AS u
                                    INNER JOIN UtilisateursRoles AS r ON u.matricule = r.matricule_id
                                    WHERE u.matricule = '$id' AND u.is_deleted = FALSE";
                        $exe_requete = $con->query($requete);
                        $roles = [];
                        while ($rol = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                            $roles[] = $rol['role_id'];
                        }

                        // Définir les rôles pour chaque catégorie de vérification
                        $adminTable = [6, 8, 12];
                        $ajoutCompTable = [3];
                        $voirCompTable = [2];
                        $progReunTable = [5];
                        $ajoutUserTable = [11];
                        $voirUserTable = [9];
                        $ajoutPosteTable = [10];

                        // Déterminer si la checkbox doit être cochée pour chaque catégorie
                        $isAdminChecked = !empty(array_intersect($adminTable, $roles));
                        $isAjoutCompChecked = !empty(array_intersect($ajoutCompTable, $roles));
                        $isVoirCompChecked = !empty(array_intersect($voirCompTable, $roles));
                        $isProgReunChecked = !empty(array_intersect($progReunTable, $roles));
                        $isAjoutUserChecked = !empty(array_intersect($ajoutUserTable, $roles));
                        $isVoirUserChecked = !empty(array_intersect($voirUserTable, $roles));
                        $isAjoutPosteChecked = !empty(array_intersect($ajoutPosteTable, $roles));
                    }
                    ?>
                    <table>
                        <tr>
                            <td><label for="">-- Matricule: </label></td>
                            <td colspan="2"><input type="text" value="<?php echo isset($row1['matricule']) ? $row1['matricule'] : ''; ?>" name="matricule" size="12" required></td>
                            <td style="color: #6124578; padding-left: 200px;"><h4>PERMISSIONS</h4></td>
                        </tr>
                        <tr>
                            <td><label for="">-- Mot de passe:</label></td>
                            <td colspan="2">
                                <input type="password" name="motPasse" minlength="4" <?php echo !isset($_GET['id']) ? "required" : ''; ?>>
                                <label for="">&nbsp;&nbsp; Confirmer le mot de passe:</label>
                                <input type="password" name="confirm_motPasse" <?php echo !isset($_GET['id']) ? "required" : ''; ?>>
                            </td>
                            <td style="padding-left: 200px;">
                              <?php if ($directeurConnect || $adminRole) : ?>
                                Administrateur&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="admin"
                                <?php if (!empty($id)){if ($isAdminChecked) echo 'checked';} ?>>
                              <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="">-- Nom:</label></td>
                            <td colspan="2"><input type="text" id="majuscules" minlength="3" oninput="this.value = this.value.toUpperCase()" value="<?php echo isset($row1['nom']) ? $row1['nom'] : ''; ?>" name="nom" size="15" required></td>
                            <td style="padding-left: 200px;">
                                Ajout de compte-rendu&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="ajoutComp"
                                <?php if (!empty($id)){if($isAjoutCompChecked) echo 'checked';} else {echo 'checked';} ?>>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="">-- Prénom:</label></td>
                            <td colspan="2"><input type="text" id="majuscules" minlength="3" oninput="this.value = this.value.toUpperCase()" value="<?php echo isset($row1['prenom']) ? $row1['prenom'] : ''; ?>" name="prenom" size="45" required></td>
                            <td style="padding-left: 200px;">
                                Voir la liste des comptes-rendus&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="voirComp"checked onclick="return false;" disabled
                                <?php if (!empty($id)){if($isVoirCompChecked) echo 'checked';} else {echo 'checked';} ?>>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="">-- Contact:</label></td>
                            <td colspan="2"><input type="text" size="10" id="contact" inputmode="numeric" pattern="[0-9]*" minlength="10" maxlength="10" value="<?php echo isset($row1['contact']) ? $row1['contact'] : ''; ?>" name="contact" required></td>
                            <td style="padding-left: 200px;">
                                Programmer des réunions&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="progReun" <?php if (!empty($id) && $isProgReunChecked) echo 'checked'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="">-- Email:</label></td>
                            <td colspan="2"><input type="email" id="email" oninput="this.value = this.value.toLowerCase()" value="<?php echo isset($row1['mail']) ? $row1['mail'] : ''; ?>" name="email" size="38"></td>
                            <td style="padding-left: 200px;">
                                Ajouter des utilisateurs&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ajoutUser" <?php if (!empty($id) && $isAjoutUserChecked) echo 'checked'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <?php if (!empty($id) && $id == $matriculeDirecteur): ?>
                                <td><label for="">-- Direction:</label></td>
                            <?php else: ?>
                                <td><label for="">-- Sous-direction:</label></td>
                            <?php endif; ?>
                            <td colspan="2">
                                <select id="select_direct" name="select_direct" required>
                                    <option value="" disabled <?php if (empty($id)) echo "selected"; ?>>--Sélectionner la direction--</option>
                                    <?php
                                    if (!empty($id) && $id == $matriculeDirecteur) {
                                        $requete = "SELECT * FROM directions WHERE id_direction = 0";
                                    } else {
                                      if ($directeurConnect || $adminRole) {
                                        $requete = "SELECT * FROM directions WHERE id_direction <> 0 AND is_deleted = FALSE ORDER BY libelle_direction";
                                      }else {
                                        $requete = "SELECT * FROM directions WHERE id_direction <> 0 AND id_direction = ".$rowId['id_direction']." AND is_deleted = FALSE ORDER BY libelle_direction";
                                      }
                                    }
                                    $exe_requete = $con->query($requete);
                                    if ($exe_requete->rowCount() > 0) {
                                        while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ($row['id_direction'] == $selectedDirectionID) ? 'selected' : '';
                                            echo "<option value='" . $row['id_direction'] . "' $selected>" . $row['libelle_direction'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="padding-left: 200px;">
                                Voir la liste des utilisateurs&nbsp;&nbsp;&nbsp;<input type="checkbox" name="voirUser" <?php if (!empty($id) && $isVoirUserChecked) echo 'checked'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="">-- Poste:</label></td>
                            <td colspan="2">
                                <select id="select_poste" name="select_poste" required>
                                    <option value="" disabled <?php if (empty($id)) echo "selected"; ?>>--Sélectionner d'abord la direction--</option>
                                    <!-- Les options seront ajoutées par AJAX -->
                                </select>
                            </td>
                            <td style="padding-left: 200px;">
                                Ajouter des postes&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ajoutPoste" <?php if (!empty($id) && $isAjoutPosteChecked) echo 'checked'; ?>>
                            </td>
                        </tr>
                    </table>
                    <br><br>
                    <center>
                        <?php if (!empty($id)) { ?>
                            <!-- Champ caché pour envoyer l'ancien id (matricule) dans traitement.php -->
                            <input type="hidden" class="form-control" name="id" value="<?php echo $id; ?>">
                            <!-- Bouton de soumission -->
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <button type="submit" class="btn btn-primary" name="bt_modif">Modifier</button>
                                </div>
                                <div class="">
                                    <a href="javascript:history.go(-1)" class="bg-danger">Annuler la modification</a>
                                </div>
                            </div>
                            <!--/ Bouton de soumission -->
                        <?php } else { ?>
                            <!-- Bouton de soumission -->
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <input class="bg-success" type="submit" name="bt_ajout" value="Ajouter">
                                </div>
                                <div class="">
                                    <input class="bg-danger" type="reset" name="bt_annul" value="Annuler">
                                </div>
                            </div>
                            <!-- Bouton de soumission -->
                        <?php } ?>
                    </center>
                </form>
            </div>
            <!-- /direct-chat-infos clearfix -->
        </div>
        <!-- /.direct-chat-msg -->
    </div>
    <!-- /.direct-chat-messages -->
</div>
<!-- /.card-body -->
