
<div class="card-header">
<h3 class="card-title">Ajouter des postes</h3>
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
         <a href="#div1">Voir la lIste postes</a>
     </div>
     <?php if (in_array(11, $roles) || in_array(12, $roles) ) : ?>
       <div class=" ">
         <?php if ($directeurConnect || $adminRole || $ajoutUserRole) { ?>
          <a href="ajout-utilisateur.php">Ajouter un utilisateur</a>
        <?php } ?>
       </div>
     <?php endif;
     if ($directeurConnect || $adminRole) : ?>
         <div class="">
            <a href="ajout-direction.php">Ajouter une sous-direction</a>
        </div>
      <?php endif; ?>
    </div>
  <!-- /lien horizontal-->
<!-- Conversations are loaded here -->
<div class="direct-chat-messages">
  <!-- Message. Default to the left -->
  <div class="direct-chat-msg">
    <div class="direct-chat-infos clearfix">
        <form style="background-color:#bbbbbb;padding:15px" class="formulaires" action="traitement.php" method="post"
        <?php if (!isset($_GET['modPoste'])) { ?>
          onsubmit="return confirm('Êtes-vous sûr de ajouter ce poste ?')"
        <?php }else { ?>
          onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce poste ?')">
        <?php } ?>
        >
          <?php
      //En cas de modification du poste
            if (isset($_GET['modPoste'])) {
                  $idPoste= $_GET['modPoste'];
                    // requete si le parametre modify est envoyé dans cette adresse
                    $requete="SELECT *  FROM postes AS p
                    INNER JOIN directions AS d ON
                    p.directions_id=d.id_direction
                    WHERE id_poste='$idPoste' ";
                    $exe_requete= $con->query($requete);
                    $row2= $exe_requete->fetch(PDO::FETCH_ASSOC);
                  }
                   ?>
          <table id="table">
                <tr>
                  <?php if (isset($idPoste)AND $idPoste==0) {
                    echo "<td> <label for=''>--direction: &nbsp;&nbsp;</label> </td>";
                  }else {?>
                     <td> <label for="">-sous-direction: &nbsp;&nbsp;</label> </td>
                   <?php } ?>
<?php             //liste deroulante des directions                         ?>
                     <td> <select class="" name="select_direct" required>
                       <option value=""disabled <?php if(!isset($idPoste)) echo "selected"; ?>>---Sélectionner la direction---</option>
                       <?php
                          if (isset($idPoste)AND $idPoste==0) {
                            $requete="SELECT * FROM directions WHERE id_direction=0 AND is_deleted=FALSE";
                          }else {
                                if ($directeurConnect || $adminRole) {
                                  $requete="SELECT * FROM directions WHERE id_direction<>0 AND is_deleted=FALSE";
                                }else {
                                  $requete="SELECT * FROM directions WHERE id_direction<>0 AND libelle_direction='".$rowId['libelle_direction']."'
                                  AND is_deleted=FALSE";
                                }
                              }
                            $exe_requete= $con->query($requete);
                             if ($exe_requete->rowCount() > 0) {
                               // code...
                               while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                     echo "<option value='".$row['id_direction']."'";
                                              if (isset($idPoste) && $row['id_direction']===$row2['id_direction']) {
                                                echo "selected";
                                              }
                                          echo">".$row['libelle_direction']."</option>";
                               }
                             }
                        ?>
                           </select> </td>
                  </tr>
                  <tr>
                     <td> <label for="">-- Poste:</label> </td>
                     <td> <input type="text" value="<?php if (isset($idPoste)){echo $row2['libelle_poste']; }?>" name="poste" size=35"" required> </td>
                  </tr>
                </table>   <br> <br>
                              <center>
                                <?php if (isset($idPoste)) {
                                  ?>
                                  <!--Chanp caché pour envoyé id_poste dans traitement.php     -->
                                      <input type="hidden" class="form-control" name="idPoste"value="<?php echo $idPoste; ?>">
                                      <!-- Bouton de soumission -->
                                      <div class="d-flex justify-content-between">
                                      <div class="">
                                      <input class="bg-success" type="submit" name="bt_modif_poste" value="Modifier">&nbsp;&nbsp;
                                    </div>
                                    <div class="">
                                      <a href='javascript:history.go(-1)'class='bg-secondary'>Annuler la modification</a>
                                    </div>
                                  </div>
                                  <!-- /Bouton de soumission -->
                                  <?php
                                }  else {
                                  ?>
                                  <!-- Bouton de soumission -->
                                  <div class="d-flex justify-content-between">
                                  <div class="">
                                   <input class="bg-success" type="submit" name="bt_ajout_post" value="Ajouter">
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
                           </div>
                           <!-- /.card-body -->
<!--************ Affichage se la liste des postes******************* -->
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
                          &nbsp;&nbsp;<a href="<?php echo $url ?>"><i class="fas fa-pen text-success">Modifier le poste de <?php echo $poste ?></i></a>
                       </div>
                     </th>
                 </tr>
               <?php } ?>
               </table>
              </div>
            <?php
          }  //FIN if ($directeurConnect || $adminRole)
            if ($directeurConnect || $adminRole) {
              $requete="SELECT p.libelle_poste, p.id_poste,
                                d.libelle_direction, d.id_direction,
                                p.mise_a_jour
                FROM directions AS d
              INNER JOIN postes AS p ON
              d.id_direction=p.directions_id WHERE p.id_poste<>0 AND p.is_deleted=FALSE
              ORDER BY d.libelle_direction
              ";
            }else {
              $requete="SELECT * FROM postes AS p
               INNER JOIN directions AS d ON d.id_direction=p.directions_id
               WHERE p.id_poste<>0 AND d.libelle_direction='".$rowId['libelle_direction']."'
                 AND p.is_deleted=FALSE";
            }
            //........Executati0n des requete4...............................
                       ?>
                           <div class="card-body">
                             <table id="table" class="table table-bordered table-hover">
                               <thead>
                               <tr>
                                 <th>Sous-directions</th>
                                 <th class="text-info">Postes</th>
                                 <th>Mise à jour</th>
                                 <th>
                                   <div class="d-flex justify-content-between">
                                    <div class="text-success">Modifier</div>
                                    <div class="text-danger">Supprimer</div>
                                  </div>
                                 </th>
                              </tr>
                              </thead>
                              <tbody>
                               <?php
                                  $exe_requete= $con->query($requete);
                              while($row= $exe_requete->fetch(PDO::FETCH_ASSOC))
                                {
                                     $direction= $row['libelle_direction'];
                                     $poste= $row['libelle_poste'];
                                     $idDirection= $row['id_direction'];
                                     $idPoste= $row['id_poste'];
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
                                 <th><?php echo $direction ?></th>
                                 <th style="color:#0000cc"><?php echo $poste ?></th>
                                 <td><?php echo $formattedDate ?></td>
                                 <th>
                                 <?php
                                    $url="ajout-poste.php?modPoste=".urlencode($idPoste);
                                 ?>
                                 <div class="d-flex justify-content-between">
                                   <div class="">
                                    <a href="<?php echo $url ?>"><i class="fas fa-pen text-success"></i></a>
                                  </div>
                                    <?php
                                    $url="traitement.php?supPoste=".urlencode($idPoste);
                                    ?>
                                    <div class="">
                                    <a href="<?php echo $url ?>"
                                      onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce poste ?');"
                                      ><i class="fas fa-trash text-danger"></i></a>
                                  </div
                                </div>
                                 </th>
                              </tr>
                            <?php } ?>
                          </tbody>
                            </table>
                          </div>
