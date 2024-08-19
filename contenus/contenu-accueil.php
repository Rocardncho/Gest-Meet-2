<div class="container">
  <!-- Affichage des données PHP -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-lg-3
    col-6">
      <!-- small box -->
      <div class="small-box bg-info" style="color: #fff; padding: 20px;">
        <div class="inner">
          <h4 style="font-size: 20px;">Réunions <br> Programmées</h4>
          <?php
            $date= new DateTime();
            $dateFormat=$date->format('Y-m-d');
            if ($directeurConnect) {
              $requete= "SELECT COUNT(*) AS total FROM reunions WHERE date_reunion >= '$dateFormat'";
            }else {
              $requete= "SELECT COUNT(*) AS total FROM reunions WHERE date_reunion >= '$dateFormat'
               AND (directions_id=".$rowId['id_direction']." or directions_id=0)
               ";
            }
            $exe_requete= $con->query($requete);
            if ($exe_requete) {
              $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
              echo "<p>".$row['total']." disponible(s)</p>";
            }
          ?>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <?php
          $parametre= 'réunions-programmées';
          $url= 'reunions.php?parametre='.urlencode($parametre);
        ?>
        <a href="<?php echo $url ?>" class="small-box-footer" style="color: #fff;">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-success" style="color: #fff; padding: 20px;">
        <div class="inner">
          <h4 style="font-size: 20px;">Comptes-rendus <br>disponibles</h4>
          <?php
          if ($directeurConnect) {
            $requete= "SELECT COUNT(*) AS total FROM reunions WHERE compte_rendu IS NOT NULL";
          }else {
            $requete= "SELECT COUNT(*) AS total FROM reunions
             WHERE compte_rendu IS NOT NULL
             AND (directions_id=".$rowId['id_direction']." or directions_id=0)
             ";
          }
            $exe_requete= $con->query($requete);
            if ($exe_requete) {
              $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
              echo "<p>".$row['total']." disponible(s)</p>";
            }
          ?>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="liste-des-comptes-rendus.php" class="small-box-footer" style="color: #fff;">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-warning" style="color: #fff; padding: 20px;">
        <div class="inner">
          <h4 style="font-size: 20px;">Comptes-rendus <br>en attente</h4>
          <?php
          if ($directeurConnect) {
            $requete= "SELECT COUNT(*) AS total FROM reunions WHERE compte_rendu IS NULL AND date_reunion < '$dateFormat'";
          }else {
            $requete= "SELECT COUNT(*) AS total FROM reunions WHERE compte_rendu IS NULL AND date_reunion < '$dateFormat'
             AND (directions_id=".$rowId['id_direction']." or directions_id=0)
             ";
          }
            $exe_requete= $con->query($requete);
            if ($exe_requete) {
              $row= $exe_requete->fetch(PDO::FETCH_ASSOC);
              echo "<p>".$row['total']." en attente(s)</p>";
            }
          ?>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <?php
          $para= 'comptes-rendus-en-attentes';
          $url= 'reunions.php?para='.urlencode($para);
        ?>
        <a href="<?php echo $url; ?>" class="small-box-footer" style="color: #fff;">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col 3-->
    <div class="col-lg-3 col-6">
      <img src="dist/img/sale-de-reunion.jpg" height="185" alt="">
    </div>
    <!-- ./col 4-->
  </div>
  <!--/row -->
  <!-- Calendrier et Formulaire de Modification de Mot de Passe -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-lg-6">
      <!-- Section pour les informations utiles -->
      <div class="card">
        <div class="card-body">
          <!-- Formulaire de modification de mot de passe -->
             <form style="background-color:#bbbbbb;padding:15px;height:300px" class="formulaires" action="traitement.php" method="post"
              onsubmit="return confirm('Êtes-vous sûr de vouloir modifier le mot de passe ?')">
               <caption><h3><center>Modifier votre mot de passe</center></h3></caption><br><br>
             <center>  <table>
              <tr>
                <td><label for=''>-- Mot de passe:</label></td>
                <td><input type="password" name="motPasse" minlength="4" required></td>
              </tr>
              <tr>
                <td><label for="">-- Confirmation:</label></td>
                <td><input type="password" minlength="4" name="conf" required></td>
              </tr>
            </table></center>
            <br><br>
            <center>
           <!-- Bouton de soumission -->
            <input class="bg-success" type="submit" name="bt_modif_motPase" value="Modifier">
           </center>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <h3>Calendrier</h3>
      <iframe src="https://calendar.google.com/calendar/embed?src=fr.ci%23holiday%40group.v.calendar.google.com&ctz=Africa%2FAbidjan" style="border: 0" width="100%" height="300" frameborder="0" scrolling="no"></iframe>
    </div>
  </div>
</div>
