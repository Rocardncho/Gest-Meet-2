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
     <!-- Conversations are loaded here -->
     <div class="direct-chat-messages">
       <!-- Message. Default to the left -->
       <div class="direct-chat-msg">
         <div class="direct-chat-infos clearfix">
             <form style="background-color:#dddddd;padding:15px" class="formulaires" action="traitement.php" method="post">

               <table>
                 <tr>
                       <td><label for="">-- Date: </label></td>
                         <td> <input type="date" name="dateReunion" required></td>
                 </tr>
                 <tr>
                     <td><label for="">-- De:</label></td>
                     <td> <input type="time" name="heureDebut" required >
                 </tr >
                 <tr>
                     <td><label for="">-- à:</label></td>
                     <td> <input type="time" name="heureFin" required> </td>
                 </tr>
                   <tr>
                         <td> <label for="">-- Ordre du jour:</label> </td>
                         <td> <input type="text" name="ordreJour" size="35" required> </td>
                   </tr>
                       <tr>
                           <td> <label for="">-- Coatch de la réunion:</label> </td>
                           <td> <select class="" name="coatch" required>
                             <?php
                                 $requete="SELECT * FROM utilisateurs";
                                     $exe_requete= $con->query($requete);
                                   if ($exe_requete->rowCount() > 0) {
                                     // code...
                                     while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                           echo "<option value='".$row['matricule']."'>".$row['nom']." ".$row['prenom']."</option>";
                                     }
                                   }
                              ?>
                                 </select> </td>
                                       </tr>
                                       <tr>
                                         <td> <label for="">-- Chargé du compte-rendu:</label> </td>
                                         <td> <select class="" name="charge" required>
                                           <?php
                                               $requete="SELECT * FROM utilisateurs
                                               WHERE matricule <>'directeur'AND matricule <> 'directeurRH'
                                                   ";
                                                   $exe_requete= $con->query($requete);
                                                 if ($exe_requete->rowCount() > 0) {
                                                   // code...
                                                   while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                                         echo "<option value='".$row['matricule']."'>".$row['nom']." ".$row['prenom']."</option>";
                                                   }
                                                 }
                                            ?>
                                         </select> </td>
                                       </tr>

                                     </table>   <br> <br>
                                     <center>
                                       <?php
                                            $requete="SELECT nom, prenom FROM utilisateurs
                                            WHERE nom_utilisateur='$username'";
                                            $exe_requete= $con->query($requete);
                                            $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                       <label for="">Programmée par:
                                         <input type="text" name="auteur" value="<?php echo $row['nom'].' '.$row['prenom']; ?>" readonly> </label>
                                     </center><br>
                                     <center>
                                        <input class="bg-success" type="submit" name="bt_prog" value="Programmer">&nbsp;&nbsp;
                                        <input class="bg-danger" type="reset" name="bt_annul" value="Annuler">
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
