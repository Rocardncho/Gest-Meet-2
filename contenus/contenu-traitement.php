<?php
if ($_SERVER['REQUEST_METHOD']==='POST')
{
  if (isset($_POST['bt_enreg'])) {
    $fichier_pdf = $_FILES['fichier_pdf'];
    $select_id_date = $_POST['select_date'];
    $select_id_objet = $_POST['select_objet'];

    $chemin = "dist/fichiers/";
    $cheminFichier = $chemin . basename($fichier_pdf['name']);

    if (!is_dir($chemin)) {
        if (!mkdir($chemin, 0777, true)) {
            die("Erreur : Impossible de créer le répertoire de destination.");
        }
    }

    if ($fichier_pdf['error'] !== UPLOAD_ERR_OK) {
        die("Erreur de téléchargement : " . $fichier_pdf['error']);
    }

    if ($select_id_objet != $select_id_date) {
        echo "<center><h2 class='bg-danger'>Echec de l'ajout <br/>La date de la réunion et l'ordre du jour ne correspondent pas!</h2></center>
              <center>
              <a href='javascript:history.go(-1)' class='bg-secondary'>Réessayer</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </center>";
    }
    elseif (!is_writable($chemin)) {
        die("Erreur : Le répertoire de destination n'est pas accessible en écriture.");
    }
    elseif (move_uploaded_file($fichier_pdf['tmp_name'], $cheminFichier)) {

  try{
        $requete = "UPDATE reunions SET compte_rendu='$cheminFichier'
                    WHERE id_reunion = $select_id_date";
            $exe_requete = $con->query($requete);
          } catch (Exception $e) {
              echo "Erreur SQL : " . $e->getMessage();
              exit(0);
          }
            if ($exe_requete->rowCount() > 0) {
                echo "<center><h2 class='bg-success'>Le Compte-rendu a été enregistré avec succès</h2></center>";
                echo "<center><a href='liste-des-comptes-rendus.php'>Voir la liste des comptes-rendus</a></center>";
                try{
                $requete = "SELECT r.id_reunion, d.libelle_direction
                            FROM directions AS d
                            INNER JOIN reunions AS r ON d.id_direction = r.directions_id
                            WHERE r.id_reunion = :select_id_date
                            AND r.id_reunion = :select_id_objet
                            AND r.compte_rendu = :cheminFichier";
                $stmt = $con->prepare($requete);
                $stmt->bindParam(':select_id_date', $select_id_date, PDO::PARAM_INT);
                $stmt->bindParam(':select_id_objet', $select_id_objet, PDO::PARAM_INT);
                $stmt->bindParam(':cheminFichier', $cheminFichier, PDO::PARAM_STR);
                $stmt->execute();
              } catch (Exception $e) {
                echo "Erreur SQL : " . $e->getMessage();
                  exit(0);
            }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $idReunion = $row['id_reunion'];
                $direction = $row['libelle_direction'];

                $date = new DateTime();
                $dateFormat = $date->format('Y-m-d');
                $heureFormat = $date->format('H:i:s');
                $contenuNotification = "Un compte-rendu a été ajouté pour la réunion de la $direction qui s'est tenue le";
                try{
                $sql = "SELECT reunion_id FROM notifications
                        WHERE reunion_id = $idReunion
                        AND contenu_notification LIKE 'Un compte%'";
                $stmt2 = $con->query($sql);
              } catch (Exception $e) {
                  echo "Erreur SQL : " . $e->getMessage();
                  exit(0);
              }
                if ($stmt2->rowCount() > 0) {
                  try{
                    $sql = "DELETE FROM notifications
                            WHERE reunion_id = :idReunion
                            AND contenu_notification LIKE 'Un compte%'";
                    $stmt = $con->prepare($sql);
                    $stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
                    $stmt->execute();
                  } catch (Exception $e) {
                      echo "Erreur SQL : " . $e->getMessage();
                      exit(0);
                  }
                }
                try{
                $requete = "INSERT INTO notifications(reunion_id, date_notification, heure_notification, contenu_notification)
                            VALUES (:idReunion, :dateFormat, :heureFormat, :contenuNotification)";
                $stmt = $con->prepare($requete);
                $stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
                $stmt->bindParam(':dateFormat', $dateFormat, PDO::PARAM_STR);
                $stmt->bindParam(':heureFormat', $heureFormat, PDO::PARAM_STR);
                $stmt->bindParam(':contenuNotification', $contenuNotification, PDO::PARAM_STR);
                $stmt->execute();
              } catch (Exception $e) {
                  echo "Erreur SQL : " . $e->getMessage();
                  exit(0);
              }
            } else {
                echo "<center><h2 class='bg-info'>Le Compte-rendu a déjà été ajouté !</h2></center>";
                echo "<center><a href='liste-des-comptes-rendus.php'>Voir la liste des comptes-rendus</a></center>";
            }
    } else {
        echo "Erreur : Impossible de déplacer le fichier téléchargé.";
    }
  } // FIN IF bt_enreg
  elseif (isset($_POST['bt_prog']) OR isset($_POST['bt_modif_reunion']))
   {
    $idReunionURL = isset($_POST['bt_modif_reunion']) ? $_POST['modReunion']:'';
        $idDirection= $_POST['select_direct'];
       $dateReunion= $_POST['dateReunion'];
       $heureDebut= $_POST['heureDebut'];
       $heureFin= $_POST['heureFin'];
       $ordreJour= $_POST['ordreJour'];
       $coatch= $_POST['coatch'];
       $charge= $_POST['charge'];
       $matricule= $_POST['auteur'];
            // Vérification si les donnéés sontpas répétés
              $requete="SELECT ordre_du_jour FROM reunions
              WHERE id_reunion <> '$idReunionURL'
                    AND ordre_du_jour= '$ordreJour'
                     AND date_reunion >='$dateReunion'
                      AND heure_fin >='$heureDebut'
                      AND directions_id='$idDirection'
                      AND is_deleted=FALSE ";
              $exe_requete= $con->query($requete);
              //VERIFICATION DES HEURES
              $sql="SELECT * FROM reunions
              WHERE (heure_debut <='$heureDebut'AND heure_fin >='$heureDebut')
                                                AND id_reunion <> '$idReunionURL'
                                                AND date_reunion='$dateReunion'
                                                AND directions_id=$idDirection
                                                AND is_deleted=FALSE";
              $exe_requete= $con->query($requete);
              $exe_sql= $con->query($sql);
                if ($exe_requete->rowCount() > 0) {
                  echo "<center><h2 class='bg-danger'>Echec de Programmation <br/>L'ordre du jour a déjà été définit dans une reunion programmée</h2></center>
                    <center>
                        <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ";
                        $parametre= 'réunions-programmées';
                        $url= 'reunions.php?parametre='.urlencode($parametre);
                    ?>
                       <a href='<?php echo $url ?>'><button type="button" name="button"> liste des réunions programmées</button></a>
                   </center>
              <?php
                }elseif ($exe_sql->rowCount() > 0) {
                  echo "<center><h2 class='bg-danger'>Une reunion a déjà été programmée à  cette date et heure precise </h2></center>
                  <center>
                        <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ";
                            $parametre= 'réunions-programmées';
                            $url= 'reunions.php?parametre='.urlencode($parametre);
                  ?>
                  <a href='<?php echo $url ?>'><button type="button" name="button"> liste des réunions programmées</button></a>
                 </center>
                <?php
              }elseif ($heureDebut >= $heureFin) {
                echo "<center><h2 class='bg-danger'>L'Heure de début de la réunion est supérieur ou égale à celle de la fin</h2></center>
                <center>
                      <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </center>";
              }
              elseif ($dateReunion < $dateFormat) {
                echo "<center><h2 class='bg-danger'>Vous avez choisit une date inférieur à celle d'aujourd'hui</h2></center>
                <center>
                      <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </center>";
              }
            else {
              //HEURE et DATE
              $date= new DateTime();
              $dateFormat=$date->format('Y-m-d'); //date d'auj0urd'hui
              $heure= new DateTime();
              $heureFormat=$heure->format('H:i:s'); //heure actuelle

              if (empty($idReunionURL)){
              // requet d'insertion
                $requete="INSERT INTO reunions(directions_id,date_reunion,heure_debut,heure_fin,ordre_du_jour,programmer_par)
                        VALUES($idDirection,'$dateReunion','$heureDebut','$heureFin','$ordreJour','$matricule')";
                $exe_requete= $con->query($requete);
                  //Selection de l'id de la reunion programmée
                  $requete="SELECT id_reunion FROM reunions
                  WHERE programmer_par='$matricule' ANd date_reunion= '$dateReunion' AND
                  heure_debut='$heureDebut'AND heure_fin='$heureFin' AND ordre_du_jour='$ordreJour' ";
                      $exe_requete= $con->query($requete);
                      $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                      $idReunion= $row['id_reunion'];
            $requete= "INSERT INTO designer(reunion_id,matricule_designer,role_joue)
                          VALUES($idReunion,'$coatch','Coach de la reunion')";
              $exe_requete= $con->query($requete);
              $requete= "INSERT INTO designer(reunion_id,matricule_designer,role_joue)
                            VALUES($idReunion,'$charge','Chargé du compte-rendu')";
                $exe_requete= $con->query($requete);
                //insertion de l'information de l'enregistrément dans la table notifications
                //le nom de la direction
                $requete = "SELECT libelle_direction FROM directions AS d
              INNER JOIN reunions AS r ON r.directions_id = d.id_direction
              WHERE d.id_direction = :idDirection
              LIMIT 1";
              $stmt = $con->prepare($requete);
              $stmt->bindParam(':idDirection', $idDirection, PDO::PARAM_INT);
              $stmt->execute();
              $direction = $stmt->fetchColumn();
                //INSERTION
                $requete = "INSERT INTO notifications(reunion_id, date_notification, heure_notification, contenu_notification)
              VALUES (:idReunion, :dateFormat, :heureFormat, :contenuNotification)";
  $stmt = $con->prepare($requete);
  $stmt->bindParam(':idReunion', $idReunion, PDO::PARAM_INT);
  $stmt->bindParam(':dateFormat', $dateFormat, PDO::PARAM_STR);
  $stmt->bindParam(':heureFormat', $heureFormat, PDO::PARAM_STR);
  $contenuNotification = "Une réunion de la $direction a été programmée";
  $stmt->bindParam(':contenuNotification', $contenuNotification, PDO::PARAM_STR);
  $stmt->execute();
              if ($stmt) {
                echo"<center><h2 class='bg-success'>La reunion a bien été programmée!</h2></center>
                <center>
                      <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      ";
                          $parametre= 'réunions-programmées';
                          $url= 'reunions.php?parametre='.urlencode($parametre);
                  ?>
                    <a href='<?php echo $url ?>'><button type="button" name="button"> liste des réunions programmées</button></a>
                 </center>
                <?php
              }else {
                echo "echec de la Programmation!!!";
              }
            } // FIN if (empty($idReunionURL))
            else {
              //Requete de modification
              try {
    // Début de la transaction
    $con->beginTransaction();

    //le nom de la direction
    $requete = "SELECT libelle_direction FROM directions AS d
  INNER JOIN reunions AS r ON r.directions_id = d.id_direction
  WHERE d.id_direction = :idDirection
  LIMIT 1";
  $stmt = $con->prepare($requete);
  $stmt->bindParam(':idDirection', $idDirection, PDO::PARAM_INT);
  $stmt->execute();
  $direction = $stmt->fetchColumn();
    // Mise à jour de la table notifications
    $requeteNotification = "UPDATE notifications
                          SET
                              date_notification = :newDateNotification,
                              heure_notification = :newHN,
                              mise_a_jour = :newDate,
                              contenu_notification = :newCN
                          WHERE reunion_id = :idReunionURL";

  $statementNotification = $con->prepare($requeteNotification);

  // Construire correctement la chaîne de contenu_notification avec la variable $direction interpolée
  $contenuNotification = "Une reunion de la $direction a été programmée";

  $statementNotification->execute([
      ':idReunionURL' => $idReunionURL,
      ':newDate' => $dateTimeFormat,
      ':newCN' => $contenuNotification, // Utilisation de la variable correctement interpolée
      ':newHN' => $heureFormat,
      ':newDateNotification' => $dateFormat
  ]);
    // Mise à jour du coach dans la table designer
    $requeteDesignerCoach = "UPDATE designer
                             SET matricule_designer = :coatch,
                                 mise_a_jour = :newDate
                             WHERE reunion_id = :idReunionURL AND role_joue = :roleCoach";
    $statementDesignerCoach = $con->prepare($requeteDesignerCoach);
    $statementDesignerCoach->execute([
        ':idReunionURL' => $idReunionURL,
        ':newDate' => $dateTimeFormat,
        ':roleCoach' => 'Coach de la reunion',
        ':coatch' => $coatch
    ]);

    // Mise à jour du chargé dans la table designer
    $requeteDesignerCharge = "UPDATE designer
                              SET matricule_designer = :charge,
                                  mise_a_jour = :newDate
                              WHERE reunion_id = :idReunionURL AND role_joue = :roleCharge";
    $statementDesignerCharge = $con->prepare($requeteDesignerCharge);
    $statementDesignerCharge->execute([
        ':idReunionURL' => $idReunionURL,
        ':newDate' => $dateTimeFormat,
        ':roleCharge' => 'Chargé du compte-rendu',
        ':charge' => $charge
    ]);

    // Mise à jour de la table reunions
    $requeteReunion = "UPDATE reunions
                       SET
                           directions_id = :newDirectionID,
                           date_reunion = :newDR,
                           heure_debut = :newHD,
                           heure_fin = :newHF,
                           ordre_du_jour = :newOJ,
                           mise_a_jour = :newDate
                       WHERE id_reunion = :idReunionURL";
    $statementReunion = $con->prepare($requeteReunion);
    $statementReunion->execute([
        ':idReunionURL' => $idReunionURL,
        ':newDate' => $dateTimeFormat,
        ':newOJ' => $ordreJour,
        ':newHF' => $heureFin,
        ':newHD' => $heureDebut,
        ':newDR' => $dateReunion,
        ':newDirectionID' => $idDirection
    ]);

    // Validation de la transaction
    $con->commit();
    // Affichage du message de succès et du lien
        echo "<h2 class='bg-success'><center>Modification exécutée avec succès !</center></h2>";
        $parametre2 = 'Nouvelle-reunion';
        $url = 'reunions.php?parametre2=' . urlencode($parametre2);
        echo "<center><a href='$url'>Vérifier la modification</a></center>";
} catch (Exception $e) {
    // En cas d'erreur, annulation de la transaction
    $con->rollback();
    echo "Erreur : " . $e->getMessage();
}
} // fin else
 }// fin  else
}// fin elseif bt_prog
      elseif (isset($_POST['bt_ajout'])) {
        $matricule= $_POST['matricule'];
        $motPasse= $_POST['motPasse'];
        $nom= $_POST['nom'];
        $prenom= $_POST['prenom'];
        $contact= $_POST['contact'];
        $email  = $_POST['email'];
        $idPoste  = $_POST['select_poste'];
        $idDirection= $_POST['select_direct'];
        $confirm_motPasse = $_POST['confirm_motPasse'];

       // Vérification que les mots de passe correspondent
       if ($motPasse === $confirm_motPasse) {
           // Hachage du mot de passe avant de le stocker dans la base de données
           $hashed_password = password_hash($motPasse, PASSWORD_DEFAULT);

          //requete pour selectionner si l'utilisateur esr répeté
                  $requete="SELECT matricule,contact,mail FROM utilisateurs
                  WHERE (matricule= '$matricule' OR contact='$contact' OR mail='$email')
                      AND is_deleted=FALSE
                    ";
                  $exe_requete= $con->query($requete);
                    if ($exe_requete->rowCount() > 0) {
                      echo "<h2 class='bg-danger'><center>Cet utilisateur a surément déjà été enregistré! <br/> Vérifiez vos informations</center></h2>
                      <center>
                            <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                      </center>
                      ";
                    }else {
                    //requete INSERT reparée pour évité les problemes d'apostrophe
                    $requete = "INSERT INTO utilisateurs (matricule, mot_de_passe, nom, prenom, contact, mail, poste_id)
            VALUES(:matricule, :motPasse, :nom, :prenom, :contact, :email, :idPoste)";
             //preparation de la requete
              $statement = $con->prepare($requete);
              //execution de la requete
              $statement->execute([
                ':matricule' => $matricule,
                ':motPasse' => $hashed_password,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':contact' => $contact,
                ':email' => $email,
                ':idPoste' => $idPoste
              ]);
              //Insertion dans la table  UtilisateursRoles
                if (isset($_POST['admin'])) {
             // Préparer une seule requête d'insertion pour plusieurs valeurs
               $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                VALUES (:matricule, 6), (:matricule, 8), (:matricule, 12)";
                $statement = $con->prepare($requete);
                $statement->execute([
               ':matricule' => $matricule,
               ]);
                 }
               if (isset($_POST['ajoutComp'])) {
              $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
               VALUES (:matricule, 3)";
               $statement = $con->prepare($requete);
               $statement->execute([
              ':matricule' => $matricule,
              ]);
                  }
                    if (isset($_POST['voirComp'])) {
                   $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                    VALUES (:matricule, 2)";
                    $statement = $con->prepare($requete);
                    $statement->execute([
                   ':matricule' => $matricule,
                   ]);
                 }
                 if (isset($_POST['progReun'])) {
                $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                 VALUES (:matricule, 5)";
                 $statement = $con->prepare($requete);
                 $statement->execute([
                ':matricule' => $matricule,
                ]);
                    }
                    if (isset($_POST['ajoutUser'])) {
                   $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                    VALUES (:matricule, 11)";
                    $statement = $con->prepare($requete);
                    $statement->execute([
                   ':matricule' => $matricule,
                   ]);
                       }
                       if (isset($_POST['voirUser'])) {
                      $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                       VALUES (:matricule, 9)";
                       $statement = $con->prepare($requete);
                       $statement->execute([
                      ':matricule' => $matricule,
                      ]);
                          }
                          if (isset($_POST['ajoutPoste'])) {
                         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                          VALUES (:matricule, 10)";
                          $statement = $con->prepare($requete);
                          $statement->execute([
                         ':matricule' => $matricule,
                         ]);
                             }
                if ($statement) {
                        echo "<h2 clasS ='bg-success'><center>L'utilisateur a bien été ajouté!</center></h2>
                                <center>
                                      <a class='bg-info' href='liste-des-utilisateurs.php'>Voir la liste</a>
                              </center>";
                      }else {
                        echo "Echec d'ajout de l'utilisateur";
                      }
                    }
                  }else {
                       echo "<h2 class='bg-danger'><center>Les mots de passe ne correspondent pas.</center></h2>
                       <center>
                             <a href='javascript:history.go(-1)'class='bg-secondary'>Réessayer</a>
                       </center>
                       ";
                  }
      }//fin else bt_ajout
      elseif (isset($_POST['bt_ajout_direct'])) {
        $direction= $_POST['direction'];
                  $requete="SELECT * FROM directions WHERE libelle_direction='$direction' AND is_deleted=FALSE";
                  $exe_requete= $con->query($requete);
                    if ($exe_requete->rowCount() > 0) {
                            echo "<h2 class='bg-danger'><center>Cette direction existe déjà</center></h2>
                            <center>
                                  <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                            </center>
                            ";
                          }
                          else {
                        $requete="INSERT INTO directions (libelle_direction)
                                VALUES('$direction')";
                      $exe_requete= $con->query($requete);
                      if ($exe_requete) {
                        echo "<h2 class='bg-success'><center>La direction a bien été ajoutée!</center></h2>
                        <center>
                          <a href='javascript:history.go(-1)'class='bg-secondary'>Voir la liste des directions</a>
                        </center>
                        ";
                      }else {
                        echo "Echec d'ajout de la direction";
                            }
                          }
      }// fin else bt_ajout_direct
      elseif (isset($_POST['bt_modif_direct'])) {
        $direction = $_POST['direction'];
        $idDirection=$_POST['idDirection'];
        //VERIFICATI0N
    //  if ($idDirection<>0) {
        $requete="SELECT * FROM directions
        WHERE libelle_direction = '$direction' AND is_deleted=FALSE";
        $exe_requete= $con->query($requete);
        //si la direction est répétée
          if ($exe_requete->rowCount() > 0) {
            $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                    ?>
            <h2 class='bg-danger'>La <?php echo $direction ?> existe déjà !
            </h2>
            <div class="">
              <center>
              <a href="javascript:history.go(-1)"class="bg-secondary">Retour</a>
            </center>
            </div>
                    <?php
          }else{
      //  }// fin if($idDi
          //requete UDATE prepareée de jointure
          $requete = "UPDATE directions
                    SET
                    libelle_direction = :newDirection,
                    mise_a_jour = :newDate
                    WHERE id_direction = :idDirection";
                    //Preparation de la requete4
                    $statement = $con->prepare($requete);
                    // Exécution de la requête préparée avec les paramètres
                    $statement->execute([
                        ':newDirection' => $direction,
                        ':newDate' => $dateTimeFormat,
                        ':idDirection' => $idDirection
                    ]);
                    if ($statement) {
                        echo "<h2 class='bg-success'><center>Modification executée! avec succès</center></h2>
                        <center>
                              <a class='bg-info' href='ajout-direction.php'>Voir la liste</a>
                      </center>
                        ";
                        $requete="SELECT *  FROM postes AS p
                        INNER JOIN directions AS d ON
                        p.directions_id=d.id_direction
                        WHERE d.id_direction=".$idDirection." AND p.is_deleted=FALSE
                        ORDER BY p.libelle_poste";
                        //...............Executati0n des requete4...............................
                        $exe_requete= $con->query($requete);
                        if ($exe_requete->rowCount() > 0)
                        {
// Affichage de la liste des utilisateurs après la ùodification du poste
?>
<!--MESSAGE D'ALESTE-->
<div class="alert  d-flex align-items-center" role="alert"
          style="height:10px;width:800px;margin-left:100px;margin-top:100px;background-color:#ff5555;">&nbsp;&nbsp;
  <i class="fas fa-regular fa-triangle-exclamation"></i>&nbsp;
  <div>
  <marquee scrollamount="3" behavior="scroll" direction="left" style="font-size: 20px;">
    Cette modification a entrainé la modification de la direction liée à ces postes ci-dessous.
  </marquee>
  </div>
</div>
<div class="card-body">
  <table id="example2" class="table table-bordered table-hover">
    <thead>
    <tr>
      <?php if ($idDirection==0) {
        echo "<th>Direction</th>";
      }else {
       ?>
      <th>Sous-direction</th>
    <?php } ?>
      <th>Poste</th>
    </tr>
    </thead>
    <tbody>
<!--         code php pour afficher la liste des utilisateurs     -->
<?php
  while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC))
  {
  $poste= $row['libelle_poste'];
  $direction= $row['libelle_direction'];
  $idPoste= $row['id_poste'];

  echo"
    <tr>
      <td>".$direction."</td>
      <td>".$poste."</td>";
  }
}
?>
    </tbody>
  </table>
</div>
<!-- /.card-body -->
<?php
  }else {
          echo "Echec de la modification";
                            }
      } // fin else
  }// fin elseif (isset($_POST['bt_modif_direct']))
  elseif (isset($_POST['bt_modif_motPase'])) {
      $motPasse=$_POST['motPasse'];
      $confirm_motPasse=$_POST['conf'];
    if ($motPasse !== $confirm_motPasse) {
        echo "<h2 class='bg-danger'><center>Les mots de passe ne correspondent pas.</center></h2>
              <center>
                    <a href='javascript:history.go(-1)' class='bg-secondary'>Réessayer</a>
              </center>";

    }else {
      $hashed_password = password_hash($motPasse, PASSWORD_DEFAULT);
      //requete UDATE prepareée de jointure
      $requete = "UPDATE utilisateurs
                SET
                mot_de_passe = :newModPasse";
                //Preparation de la requete4
                $statement = $con->prepare($requete);
                // Exécution de la requête préparée avec les paramètres
                $statement->execute([
                    ':newModPasse' => $hashed_password,
                ]);
                if ($statement) {
                  echo "<h2 class='bg-success'><center>Vous avez modifié votre mots de passe avec succès.</center></h2>
                      ";
                }
    }
  }
      elseif (isset($_POST['bt_modif'])) {
          $matricule= $_POST['matricule'];
          $motPasse= $_POST['motPasse'];
          $nom= $_POST['nom'];
          $prenom= $_POST['prenom'];
          $contact= $_POST['contact'];
          $email  = $_POST['email'];
          $idDirection = $_POST['select_direct'];
          $idPoste = $_POST['select_poste'];
          //recuperation de du parametre de l'url
          $id_matricule= $_POST['id'];
          $confirm_motPasse = $_POST['confirm_motPasse'];
          //Au cas ou les champs des mot de passe est vide
          if (empty($motPasse) && empty($confirm_motPasse)) {
      $requete = "SELECT mot_de_passe FROM utilisateurs
                  WHERE matricule = '$id_matricule'
                  AND is_deleted = FALSE";

      $exe_requete = $con->query($requete);
      $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
      $motPasse = $row ? $row['mot_de_passe'] : ''; // Assurez-vous que $row est défini avant d'accéder à $row['mot_de_passe']
      } elseif ($motPasse !== $confirm_motPasse) {
      echo "<h2 class='bg-danger'><center>Les mots de passe ne correspondent pas.</center></h2>
            <center>
                  <a href='javascript:history.go(-1)' class='bg-secondary'>Réessayer</a>
            </center>";
                return; // Empêche le code suivant de s'exécuter sans quitter le script complètement
  }
         //requete pour selectionner si l'utilisateur esr répeté
          $requete="SELECT matricule,contact,mail FROM utilisateurs
                     WHERE (matricule= '$matricule' OR contact='$contact' OR mail='$email') AND
                            matricule <> '$id_matricule'
                         AND is_deleted=FALSE
                       ";
                     $exe_requete= $con->query($requete);
       if ($exe_requete->rowCount() > 0) {
                         echo "<h2 class='bg-danger'><center>Cet utilisateur a surément déjà été enregistré! <br/> Vérifiez vos informations</center></h2>
                         <center>
                               <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                         </center>
                         ";
     }
  else { // MISE A JOUR DE L4UTILISATEUR
      //Cas des roles
 // Fonction pour charger les rôles de l'utilisateur
 function loadRoles($con, $id_matricule) {
     $requete = "SELECT role_id FROM UtilisateursRoles WHERE matricule_id = :id_matricule";
     $statement = $con->prepare($requete);
     $statement->execute([':id_matricule' => $id_matricule]);
     $roles = [];
     while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
         $roles[] = $row['role_id'];
     }
     return $roles;
 }

 // Charger les rôles de l'utilisateur au début
 $roles = loadRoles($con, $id_matricule);

  // Vérifie si le formulaire a été soumis et si le rôle 6 n'est pas dans les rôles de l'utilisateur
                 if (isset($_POST['admin'])){
                   if(!in_array(6, $roles)) {
                     // Préparer une seule requête d'insertion pour plusieurs valeurs
                     $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
                                 VALUES (:matricule1, 6), (:matricule2, 8), (:matricule3, 12)";
                     $statement = $con->prepare($requete);
                     $statement->execute([
                         ':matricule1' => $matricule,
                         ':matricule2' => $matricule,
                         ':matricule3' => $matricule,
                     ]);
                      }
                    }
                     if (!isset($_POST['admin'])){
                       if(in_array(6, $roles)) {
                          $requete = "DELETE FROM UtilisateursRoles
                             WHERE matricule_id = :oldMatricule AND role_id IN (6, 8, 12)";
                          $statement = $con->prepare($requete);
                          $statement->execute([
                          ':oldMatricule' => $id_matricule
                            ]);
                          }
                     }
    ////////////////////////////////////////////////////////////////////////
    if (isset($_POST['ajoutComp'])){
      if(!in_array(3, $roles)) {
        // Préparer une seule requête d'insertion pour plusieurs valeurs
         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule, 3)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule' => $matricule
         ]);
       }
     }
     if (!isset($_POST['ajoutComp'])){
       if(in_array(3, $roles)) {
           $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =3";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
    }
    ////////////////////////////////////////////////////////////////////////
    if (isset($_POST['voirComp'])){
        if(!in_array(2, $roles)) {
        // Préparer une seule requête d'insertion pour plusieurs valeurs
         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule, 2)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule' => $matricule
         ]);
       }
     }
     if (!isset($_POST['voirComp'])){
         if(in_array(2, $roles)) {
           $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =2";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
       }
    ////////////////////////////////////////////////////////////////////////
    if (isset($_POST['progReun'])){
        if(!in_array(5, $roles)) {
        // Préparer une seule requête d'insertion pour plusieurs valeurs
         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule, 5)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule' => $matricule
         ]);
       }
     }
     if (!isset($_POST['progReun'])){
         if(in_array(5, $roles)) {
           $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =5";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
    }
    ////////////////////////////////////////////////////////////////////////
    if (isset($_POST['ajoutUser'])){
        if(!in_array(11, $roles)) {
        // Préparer une seule requête d'insertion pour plusieurs valeurs
         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule, 11)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule' => $matricule
         ]);
       }
     }
     if (!isset($_POST['ajoutUser'])){
         if(in_array(11, $roles)) {
            $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =11";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
      }
    ////////////////////////////////////////////////////////////////////////
    if (isset($_POST['voirUser'])){
        if(!in_array(9, $roles)) {
        // Préparer une seule requête d'insertion pour plusieurs valeurs
         $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule_id, 9)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule_id' => $matricule
         ]);
       }
     }
     if (!isset($_POST['voirUser'])){
         if(in_array(9, $roles)) {
             $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =9";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
      }
       ////////////////////////////////////////////////////////////////////////
       if (isset($_POST['ajoutPoste'])){
           if(!in_array(10, $roles)) {
          $requete = "INSERT INTO UtilisateursRoles (matricule_id, role_id)
          VALUES (:matricule, 10)";
          $statement = $con->prepare($requete);
          $statement->execute([
         ':matricule' => $matricule
         ]);
       }
     }
     if (!isset($_POST['ajoutPoste'])){
         if(in_array(10, $roles)) {
           $requete = "DELETE FROM UtilisateursRoles
            WHERE matricule_id = :oldMatricule AND role_id =10";
         $statement = $con->prepare($requete);
         $statement->execute([
         ':oldMatricule' => $id_matricule
           ]);
         }
       }

       // Hashage du mot de passe si nécessaire
if (!empty($motPasse) && $motPasse === $confirm_motPasse) {
    $hashed_password = password_hash($motPasse, PASSWORD_DEFAULT);
}


// Requêtes SQL pour les mises à jour
$requeteReunions = "UPDATE reunions SET programmer_par = :newMatricule, mise_a_jour = :newDate WHERE programmer_par = :oldMatricule;";
$requeteDesigner = "UPDATE designer SET matricule_designer = :newMatricule, mise_a_jour = :newDate WHERE matricule_designer = :oldMatricule;";
$requeteUtilisateurs = "UPDATE utilisateurs SET matricule = :newMatricule, mot_de_passe = :motPasse, nom = :nom, prenom = :prenom, contact = :contact, mail = :email, mise_a_jour = :newDate, poste_id = :idPoste WHERE matricule = :oldMatricule;";

// Préparation des requêtes
$statementReunions = $con->prepare($requeteReunions);
$statementDesigner = $con->prepare($requeteDesigner);
$statementUtilisateurs = $con->prepare($requeteUtilisateurs);

// Définition des paramètres communs
$params = [
':newMatricule' => $matricule,
':nom' => $nom,
':prenom' => $prenom,
':contact' => $contact,
':email' => $email,
':idPoste' => $idPoste,
':newDate' => $dateTimeFormat,
':oldMatricule' => $id_matricule,
];

// Ajout du paramètre ':motPasse' dans $params en fonction de la condition
if (!empty($motPasse) && $motPasse === $confirm_motPasse) {
$params[':motPasse'] = $hashed_password;
} else {
$params[':motPasse'] = $motPasse; // Assurez-vous que $motPasse est défini, sinon il peut être vide si aucune correspondance n'est trouvée
}

// Exécution des requêtes avec les paramètres appropriés
$resultatReunions = $statementReunions->execute([
':newMatricule' => $matricule,
':newDate' => $dateTimeFormat,
':oldMatricule' => $id_matricule,
]);

$resultatDesigner = $statementDesigner->execute([
':newMatricule' => $matricule,
':newDate' => $dateTimeFormat,
':oldMatricule' => $id_matricule,
]);

$resultatUtilisateurs = $statementUtilisateurs->execute($params);

       // Vérification du succès de toutes les requêtes
       if ($resultatUtilisateurs) {
           echo "
               <h2 class='bg-success'><center>Modification exécutée avec succès !</center></h2>
               <center>
                   <a class='bg-info' href='liste-des-utilisateurs.php'>Voir la liste</a>
               </center>
           ";
       } else {
           echo "Echec de la modification";
       }
     }// FIN else {UPDATE
  }//fin elseif bt_modif
        //Ajut de pste
elseif (isset($_POST['bt_ajout_post'])) {
    // Activer le rapport d'erreurs pour voir tous les avertissements ou erreurs
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idDirection = $_POST['select_direct'];
    $poste = $_POST['poste'];
    // VERIFICATION
    try {
        // Préparation de la requête avec des paramètres pour éviter les injections SQL
        $requete = "SELECT * FROM postes AS p
                    INNER JOIN directions AS d ON d.id_direction = p.directions_id
                    WHERE p.libelle_poste = :poste AND p.directions_id = :idDirection AND p.is_deleted = FALSE";

        // Préparation de la requête
        $stmt = $con->prepare($requete);

        // Liaison des paramètres
        $stmt->bindParam(':poste', $poste, PDO::PARAM_STR);
        $stmt->bindParam(':idDirection', $idDirection, PDO::PARAM_INT);
      } catch (Exception $e) {
          echo "Erreur SQL : " . $e->getMessage();
          exit(0);
      }
        // Débogage - Avant l'exécution de la requête
        //echo "Avant l'exécution de la requête.<br>";
        // Exécution de la requête
        if (!$stmt->execute()) {
            // Si l'exécution échoue, afficher les erreurs SQL
            echo "Erreur SQL : " . implode(", ", $stmt->errorInfo());
            exit(0);
        }
        // Débogage - Après l'exécution de la requête
        //echo "Après l'exécution de la requête.<br>";
        // Vérification des résultats
        if ($stmt->rowCount() > 0) {
            // Récupération de la première ligne de résultat
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <h2 class='bg-danger'>
                <center>Le poste de <?php echo $poste; ?> existe déjà dans la <?php echo $row['libelle_direction']; ?></center>
            </h2>
            <div>
                <center>
                    <a href="javascript:history.go(-1)" class="bg-secondary">Retour</a>
                </center>
            </div>
<?php }else{
  // insertion du poste dans la table postes
try {
// Préparation de la requête avec des paramètres
$requete = "INSERT INTO postes (directions_id, libelle_poste)
          VALUES (:idDirection, :poste)";
// Préparation de la requête
$stmt = $con->prepare($requete);
// Liaison des paramètres
$stmt->bindParam(':idDirection', $idDirection, PDO::PARAM_INT);
$stmt->bindParam(':poste', $poste, PDO::PARAM_STR);
} catch (Exception $e) {
echo "Erreur SQL : " . $e->getMessage();
exit(0);
}
// Exécution de la requête
if ($stmt->execute()) {
  echo "<h2 class='bg-success'><center>Le poste a bien été ajouté!</center></h2>
        <center>
        <a href='javascript:history.go(-1)' class='bg-secondary'>Voir la liste des postes</a>
        </center>";
} else {
  echo "Échec de l'ajout du poste. Une erreur s'est produite.";
}
}//Fin else
}//fin elseil bt_ajout_post
      elseif (isset($_POST['bt_modif_poste'])) {
        $idDirection = $_POST['select_direct'];
        $poste = $_POST['poste'];
        $idPoste = $_POST['idPoste'];
        //VERIFICATI0N
        $requete="SELECT * FROM postes AS p
        INNER JOIN directions AS d ON
        d.id_direction = p.directions_id
        WHERE (p.libelle_poste = '$poste') AND p.directions_id= $idDirection
      ";
        $exe_requete= $con->query($requete);
        //si le poste est répéyé
          if ($exe_requete->rowCount() > 0) {
            $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                    ?>
            <h2 class='bg-danger'>Le postte de <?php echo $poste ?> excite déjà dans la <?php echo $row['libelle_direction'] ?>  !
            </h2>
            <div class="">
              <center>
              <a href="javascript:history.go(-1)"class="bg-secondary">Retour</a>
            </center>
            </div>
          <?php
        }else{
          //requete UDATE prepareée de jointure
          $requete = "UPDATE postes
                    SET
                    directions_id = :newDirection_id,
                    libelle_poste = :newPoste,
                    mise_a_jour = :newDate
                    WHERE id_poste = :idPoste";
                    //Preparation de la requete4
                    $statement = $con->prepare($requete);
                    // Exécution de la requête préparée avec les paramètres
                    $statement->execute([
                        ':newDirection_id' => $idDirection,
                        ':newPoste' => $poste,
                        ':newDate' => $dateTimeFormat,
                        ':idPoste' => $idPoste
                    ]);
                    if ($statement) {
                        echo "<h2 class='bg-success'><center>Modification executée! avec succès</center></h2>
                        <center>
                              <a class='bg-info' href='ajout-poste.php'>Voir la liste</a>
                      </center>
                        ";
                        $requete="SELECT *  FROM utilisateurs AS u
                        INNER JOIN postes AS p ON
                        u.poste_id=p.id_poste
                        INNER JOIN directions AS d ON
                        p.directions_id=d.id_direction
                        WHERE u.poste_id=".$idPoste." AND u.is_deleted = FALSE
                        ORDER BY d.libelle_direction,u.nom";
                        //...............Executati0n des requete4...............................
                        $exe_requete= $con->query($requete);
                        if ($exe_requete->rowCount() > 0)
                        {
// Affichage de la liste des utilisateurs après la ùodification du poste
?>
<!--MESSAGE D'ALESTE-->
<div class="alert  d-flex align-items-center" role="alert"
          style="height:10px;width:800px;margin-left:100px;margin-top:100px;background-color:#ff5555;">&nbsp;&nbsp;
  <i class="fas fa-regular fa-triangle-exclamation"></i>&nbsp;
  <div>
  <marquee scrollamount="3" behavior="scroll" direction="left" style="font-size: 20px;">
    Cette modification a entrainé la modification du poste ou de la direction de ces utilisateurs ci-dessous.
  </marquee>
  </div>
</div>
<div class="card-body">
  <table id="example2" class="table table-bordered table-hover">
    <thead>
    <tr>
      <th>Nom</th>
      <th>Prénom</th>
      <?php if($idPoste==0): ?>
      <th>Direction</th>
    <?php else: ?>
      <th>Sous-direction</th>
    <?php endif; ?>
      <th>Poste</th>
    </tr>
    </thead>
    <tbody>
<!--         code php pour afficher la liste des utilisateurs     -->
<?php
  while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC))
  {
  $matricule= $row['matricule'];
  $nom= $row['nom'];
  $prenom= $row['prenom'];
  $contact= $row['contact'];
  $Email= $row['mail'];
  $poste= $row['libelle_poste'];
  $direction= $row['libelle_direction'];

  echo"
    <tr>
      <td>".$nom."</td>
      <td>".$prenom."</td>
      <td>".$direction."</td>
      <td>".$poste."</td>"; ?>
        <?php
  }
}//fin if
?>
    </tbody>
  </table>
</div>
<!-- /.card-body -->
<?php
                      }else {
                        echo "Echec de la modification";
                            }
          } // fin else
      }// fin elseif (isset($_POST['bt_modif_poste']))
}//fin si method POST
if ($_SERVER['REQUEST_METHOD']==='GET') {
  if (isset($_GET['id_supp'])) {
  //recuperation de du parametre de l'url
  $matricule=$_GET['id_supp'];
  //requete pour voir si l'utilisateur existe
  $requete = "SELECT* FROM utilisateurs
     WHERE matricule = '$matricule' AND is_deleted=FALSE";
  $exe_sql = $con->query($requete);
  if ($exe_sql->rowCount() == 0) {
            echo "<h2 class='bg-info'><center>l'utilisateur a déjà été supprimé!!!</center></h2>
              <center>
                <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
              </center>
            ";
      }else {
          // supprimer definitivement dans la BDD
      $requete = "DELETE FROM UtilisateursRoles
         WHERE matricule_id = :oldMatricule";
      $statement = $con->prepare($requete);
      $statement->execute([
      ':oldMatricule' => $matricule
        ]);
        //requete pour voir si l'utilisateur est selectionné dans la table designer
        $requete = "SELECT* FROM designer
           WHERE matricule_designer = '$matricule'";
        $exe_sql = $con->query($requete);
        if ($exe_sql->rowCount()>0) {
          // is_deleted devient true
        $requete="UPDATE utilisateurs SET is_deleted = TRUE
         WHERE matricule='$matricule'";
         $statement=$con->query($requete);
          }else {
             // supprimer definitivement dans la BDD
             $requete = "DELETE FROM utilisateurs
                WHERE matricule = :oldMatricule";
             $statement = $con->prepare($requete);
             $statement->execute([
             ':oldMatricule' => $matricule
               ]);
           } // FIN supprimer definitivement dans la BDD
            if ($statement) {
                      echo "<h2 class='bg-success'><center>l'utilisateur a bien été supprimé!!!</center></h2>
                        <center>
                          <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                        </center>
                      ";
                }else {
                echo "Impossible de supprimer l'utilisateur";
                    }
          }// fin else
        }//fin if (isset($_GET[id_supp]))
        elseif (isset($_GET['supReunion'])) {
          $idReunion=$_GET['supReunion'];
          // en cas de repetition
          $requete = "SELECT * FROM reunions
             WHERE id_reunion = :oldMatricule";
          $statement = $con->prepare($requete);
          $statement->execute([
          ':oldMatricule' => $idReunion
            ]);
         if ($statement->rowCount()==0) {
                   echo "<h2 class='bg-info'><center>
                   La réunion a déjà été supprimée!!!</center></h2>
                     <center>
                       <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                     </center>
                   ";
     }else{
          // supprimer definitivement dans la BDD
          $requete = "DELETE FROM notifications
             WHERE reunion_id = :oldMatricule";
          $statement = $con->prepare($requete);
          $statement->execute([
          ':oldMatricule' => $idReunion
        ]);
        $requete = "DELETE FROM designer
           WHERE reunion_id = :oldMatricule";
        $statement = $con->prepare($requete);
        $statement->execute([
        ':oldMatricule' => $idReunion
          ]);
          $requete = "DELETE FROM reunions
             WHERE id_reunion = :oldMatricule";
          $statement = $con->prepare($requete);
          $statement->execute([
          ':oldMatricule' => $idReunion
            ]);
         if ($statement) {
                   echo "<h2 class='bg-success'><center>La réunion a bien été supprimée!!!</center></h2>
                     <center>
                       <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                     </center>
                   ";
             }else {
             echo "Impossible de supprimer";
                 }
          }//fin else
        }//elseif
        elseif (isset($_GET['supCompte'])) {
        //recuperation de du parametre de l'url
        $idReunion=$_GET['supCompte'];
        //requete pour voir si le compte rendu existe
        $requete = "SELECT* FROM reunions
           WHERE id_reunion = $idReunion AND compte_rendu IS NOT NULL";
        $exe_sql = $con->query($requete);
        if ($exe_sql->rowCount() == 0) {
                  echo "<h2 class='bg-info'><center>le compte-rendu a déjà été supprimé!!!</center></h2>
                    <center>
                      <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                    </center>
                  ";
        }else{
            // supprimer definitivement dans la BDD
            $requete = "UPDATE reunions
            SET compte_rendu = NULL
               WHERE id_reunion = :oldMatricule";
            $statement_update = $con->prepare($requete);
            $update_success=$statement_update->execute([
            ':oldMatricule' => $idReunion
              ]);
                   // supprimer definitivement dans la BDD
                   $requete = "DELETE FROM notifications
                      WHERE reunion_id = :oldMatricule";
                   $statement_delete = $con->prepare($requete);
                  $delete_success=$statement_delete->execute([
                   ':oldMatricule' => $idReunion
                     ]);
                  if ($update_success && $delete_success) {
                            echo "<h2 class='bg-success'><center>le compte-rendu a bien été supprimé!!!</center></h2>
                              <center>
                                <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                              </center>
                            ";
                      }else {
                      echo "Impossible de supprimer le compte-rendu";
                          }
                  }// fin else
              }//fin if (isset($_GET[supCompte]))
          elseif (isset($_GET['supPoste'])) {
              // code...suppression du poste
              //recuperation de du parametre de l'url
              $idPoste= $_GET['supPoste'];
                $requete="UPDATE postes SET is_deleted = TRUE
                 WHERE id_poste='$idPoste'";
                $exe= $con->query($requete);
                      if ($exe) {
                              echo "<h2 class='bg-success'>le poste a bien été supprimé!!!</h2>
                                <center>
                                  <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                                </center>
                              ";
                        }else {
                        echo "Impossible de supprimer le poste";
                            }
            } // fin elseif (isset($_GET['supPoste'])
            elseif (isset($_GET['supDirection'])) {
              // code...suppression e la direction
              //recuperation de du parametre de l'url
              $idDirection= $_GET['supDirection'];
                $requete="UPDATE directions SET is_deleted = TRUE
                 WHERE id_direction='$idDirection'";
                $exe= $con->query($requete);
                      if ($exe) {
                              echo "<h2 class='bg-success'><center>la direction a bien été supprimé!!!</center></h2>
                                <center>
                                  <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>
                                </center>
                              ";
                        }else {
                        echo "Impossible de supprimer la direction";
                            }
            } // fin elseif (isset($_GET['supDirection'])
}//fin if method GET
 ?>
