<div class="card-header">
<h3 class="card-title">Programmez une réunion</h3>
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
    <?php
        $parametre= 'réunions-programmées';
        $url= 'reunions.php?parametre='.urlencode($parametre);
      ?>
       <a href='<?php echo $url ?>'>Voir la liste des réunions programmées</a>     </div>
         <div class="">
            <a href="liste-des-comptes-rendus.php">liste des comptes-rendus</a>
        </div>
    </div>
  <!-- /lien horizontal-->
<!-- Conversations are loaded here -->
<div class="direct-chat-messages">
  <!-- Message. Default to the left -->
  <div class="direct-chat-msg">
    <div class="direct-chat-infos clearfix">
      <?php   //if (isset($_GET['modReunion'])) {
        //Vérifie si le parametre modReunion dans l'url
        $idReunion = isset($_GET['modReunion']) ? $_GET['modReunion'] : '';
       ?>
        <form style="background-color:#dddddd;padding:15px" class="formulaires" action="traitement.php" method="post"
        <?php if (empty($idReunion)) { ?>
          onsubmit="return confirm('Êtes-vous sûr de vouloir programmer cette réunion ?')"
        <?php }else { ?>
          onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cette réunion ?')">
        <?php } ?> >
          <table>
            <tr>
                  <td colspan="2"><center>
                    <label for="">Direction ou Sous-direction <br/>
  <?php
  // Récupérer les informations de la reunion  passé dans l'URL
  $requete = "SELECT * FROM reunions
              WHERE id_reunion = :id_reunion AND is_deleted = FALSE";
              $stmt = $con->prepare($requete);
                  $stmt->bindParam(':id_reunion', $idReunion, PDO::PARAM_STR);
              $stmt->execute();
              $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row1) {
                  $selectedIDirection = $row1['directions_id'];
              } else {
                  $selectedIDirection = '';
              }
// Récupérer les informations du chargé passé dans l'URL
$stmt = $con->prepare("
    SELECT * FROM designer
    WHERE reunion_id = :idReunion AND role_joue='Chargé du compte-rendu'");
$stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
$stmt->execute();
$charge = $stmt->fetch(PDO::FETCH_ASSOC);

if ($charge) {
    $selectedIDcharge = $charge['matricule_designer'];
} else {
    $selectedIDcharge = '';
}

// Récupérer les informations du coatch passé dans l'URL
$stmt = $con->prepare("
    SELECT * FROM designer
    WHERE reunion_id = :idReunion AND role_joue='Coach de la reunion'");
$stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
$stmt->execute();
$coatch = $stmt->fetch(PDO::FETCH_ASSOC);

if ($coatch) {
    $selectedIDcoatch = $coatch['matricule_designer'];
} else {
    $selectedIDcoatch = '';
}

  // Récupérer les informations du chargé passé dans l'URL
  $stmt = $con->prepare("
      SELECT * FROM designer
      WHERE reunion_id = :idReunion AND role_joue='Chargé du compte-rendu'");
      $stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
      $stmt->execute();
      $charge = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($charge) {
      $selectedIDcharge = $charge['matricule_designer'];
  } else {
      $selectedIDcharge = '';
  }
//}// FIN   if (isset($_GET['modReunion']))

// Liste déroulante des directions
if ($directeurConnect) {
    $requete = "SELECT id_direction, libelle_direction FROM directions WHERE is_deleted = FALSE
    ORDER BY libelle_direction";
    $stmt = $con->prepare($requete);
   $stmt->execute();
} else {
    $requete = "SELECT id_direction, libelle_direction FROM directions
                WHERE libelle_direction = :libelle_direction AND is_deleted = FALSE";
                $stmt = $con->prepare($requete);
                    $stmt->bindParam(':libelle_direction', $rowId['libelle_direction'], PDO::PARAM_STR);
                $stmt->execute();
}
//La fonction htmlspecialchars en PHP est utilisée pour convertir des caractères spéciaux en entités HTML.
       ?>
                    <select class="" name="select_direct" required>
                         <?php
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $selected = ($row['id_direction'] == $selectedIDirection) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['id_direction']) . "'$selected>" . htmlspecialchars($row['libelle_direction']) . "</option>";
                              }
                        //La fonction htmlspecialchars en PHP est utilisée pour convertir des caractères
                        //spéciaux en entités HTML. Cela est essentiel pour empêcher les attaques XSS
                        // (Cross-Site Scripting) en échappant les
                        // caractères spéciaux qui pourraient être interprétés comme du code HTML ou JavaScript.
                          ?>
                     </select> </label>
                  </center></td>
            </tr>
            <tr>
                  <td><label for="">-- Date de la réunion: </label></td>
                    <td> <input type="date" name="dateReunion"
                           value="<?php echo isset($row1['date_reunion']) ? $row1['date_reunion'] : ''; ?>"
                       required></td>
            </tr>
            <tr>
                <td><label for="">-- Heure de début:</label></td>
                <td> <input type="time" name="heureDebut"
                      value="<?php echo isset($row1['heure_debut']) ? $row1['heure_debut'] : ''; ?>"
                   required >
            </tr >
            <tr>
                <td><label for="">-- Heure de fin:</label></td>
                <td> <input type="time" name="heureFin"
                      value="<?php echo isset($row1['heure_fin']) ? $row1['heure_fin'] : ''; ?>"
                   required> </td>
            </tr>
              <tr>
                    <td> <label for="">-- Ordre du jour:</label> </td>
                    <td> <input type="text" name="ordreJour" size="75" minlength="6"
                            value="<?php echo isset($row1['ordre_du_jour']) ? $row1['ordre_du_jour'] : ''; ?>"
                       required> </td>
              </tr>
              <tr>
                      <td> <label for="">-- Coatch de la réunion:</label> </td>
                      <?php

    // SÉLECTION DES UTILISATEURS
    if ($directeurConnect) {
        $requete = "SELECT * FROM utilisateurs
                    WHERE is_deleted = FALSE
                    ORDER BY nom, prenom";
                    $stmt = $con->prepare($requete);
                   $stmt->execute();
    } else {
        $requete = "SELECT u.matricule, u.nom, u.prenom
                    FROM directions AS d
                    INNER JOIN postes AS p ON p.directions_id = d.id_direction
                    INNER JOIN utilisateurs AS u ON p.id_poste = u.poste_id
                    WHERE d.libelle_direction = :libelle_direction AND u.is_deleted = FALSE
                    ORDER BY u.nom, u.prenom";
                    $stmt = $con->prepare($requete);
                        $stmt->bindParam(':libelle_direction', $rowId['libelle_direction'], PDO::PARAM_STR);
                    $stmt->execute();
    }
           ?>
              <td>
                <select class="" name="coatch" required>
             <option value="" disabled <?php if(!isset($idPoste)) echo "selected"; ?>>---Sélectionner le coatch---</option>
             <?php
             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 // Vérifier la valeur de $selectedIDcoatch
                 $selected = ($row['matricule'] == $selectedIDcoatch) ? 'selected' : '';
                 echo "<option value='" . htmlspecialchars($row['matricule']) . "' $selected>" . htmlspecialchars($row['nom']) . " " . htmlspecialchars($row['prenom']) . "</option>";
             }
             ?>
         </select>
              </td>
            </tr>
                                  <tr>
                                    <?php
                                    // Cas des chargés du compte rendu
                                    if ($directeurConnect) {
                                        $requete = "SELECT * FROM utilisateurs AS u
                                            INNER JOIN UtilisateursRoles AS ur ON u.matricule = ur.matricule_id
                                                    WHERE u.is_deleted = FALSE AND ur.role_id=3
                                                    ORDER BY u.nom, u.prenom";
                                                    $stmt = $con->prepare($requete);
                                                   $stmt->execute();
                                    } else {
                                        $requete = "SELECT u.matricule, u.nom, u.prenom
                                                    FROM directions AS d
                                                    INNER JOIN postes AS p ON p.directions_id = d.id_direction
                                                    INNER JOIN utilisateurs AS u ON p.id_poste = u.poste_id
                                                    INNER JOIN UtilisateursRoles AS ur ON u.matricule = ur.matricule_id
                                                    WHERE d.libelle_direction = :libelle_direction AND u.is_deleted = FALSE
                                                          AND ur.role_id=3
                                                    ORDER BY u.nom, u.prenom";
                                                    $stmt = $con->prepare($requete);
                                                        $stmt->bindParam(':libelle_direction', $rowId['libelle_direction'], PDO::PARAM_STR);
                                                         $stmt->execute();
                                            }
                                    ?>
                                    <td> <label for="">-- Chargé du compte-rendu:</label> </td>
                                    <td> <select class="" name="charge" required>
                                      <option value=""disabled <?php if(!isset($idPoste)) echo "selected"; ?>>---Sélectionner le Chargé---</option>
                                      <?php
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($row['matricule'] == $selectedIDcharge) ? 'selected' : '';
                                     echo "<option value='" . htmlspecialchars($row['matricule']) . "'$selected>" . htmlspecialchars($row['nom']) . " " . htmlspecialchars($row['prenom']) . "</option>";
                                                 }
                                       ?>
                                    </select> </td>
                                  </tr>

                                </table>   <br> <br>
                                <center>
                                  <?php
                                       $requete="SELECT matricule FROM utilisateurs
                                       WHERE mail='$username'";
                                       $exe_requete= $con->query($requete);
                                       $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                                   ?>
                                    <input type="hidden" name="auteur" value="<?php echo $row['matricule']; ?>" readonly> </label>
                                <center>
                                  <?php if (isset($_GET['modReunion'])) {
                                              $modReunion=$_GET['modReunion'];?>
                                      <!-- Champ caché pour envoyer l'ancien id (matricule) dans traitement.php -->
                                      <input type="hidden" class="form-control" name="modReunion" value="<?php echo $modReunion; ?>">
                                      <!-- Bouton de soumission -->
                                      <div class="d-flex justify-content-between">
                                          <div class="">
                                              <button type="submit" class="btn btn-primary" name="bt_modif_reunion">Modifier</button>
                                          </div>
                                          <div class="">
                                              <a href="javascript:history.go(-1)" class="bg-danger">Annuler la modification</a>
                                          </div>
                                      </div>
                                      <!--/ Bouton de soumission -->
                                  <?php } else { ?>
                                   <input class="bg-success" type="submit" name="bt_prog" value="Programmer">&nbsp;&nbsp;
                                   <input class="bg-danger" type="reset" name="bt_annul" value="Annuler">
                                 <?php } ?>
                                </center>
                              </form>
                            </div>
                           <!-- /direct-chat-infos clearfix -->
                           </div>
                           <!-- /.direct-chat-msg -->
                           </div>
                           <!-- /.direct-chat-message -->
                           </div>
                           <!-- /.card-body -->
