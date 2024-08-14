<!-- Content Wrapper. Contains page content -->
<div class="content">
  <!-- Content Header (Page header) -->
  <?php
  //VERIFICATI0N de l'id dans l'url
  $idReunion = isset($_GET['modCompte']) ? $_GET['modCompte']:'';
if (empty($idReunion)) {
  //selectionnons la liste des chargés
  $requete="SELECT  r.id_reunion,
                    r.date_reunion,
                    d.role_joue,
                    r.ordre_du_jour
              FROM reunions AS r
              INNER JOIN designer AS d
              ON r.id_reunion = d.reunion_id
              INNER JOIN utilisateurs AS u
              ON u.matricule=d.matricule_designer
              WHERE mail='$username'
              AND d.role_joue='Chargé du compte-rendu'
              AND r.compte_rendu IS NULL
              AND date_reunion <='$dateFormat'
              ORDER BY date_reunion";
        }else {
          //selectionnons les données lié à $idReunion
          $requete="SELECT * FROM reunions
                      WHERE id_reunion=$idReunion
              ";
        }
      $exe_requete= $con->query($requete);
      $exe= $con->query($requete); // Pour la deuxieme liste deroulante
    if ($exe_requete->rowCount() > 0) {  ?>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Choisissez la date et l'objet de la reunion</h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content" >
    <div class="container-fluid" >
      <div class="row">
        <div class="col-md-6" >
            <div class="card-body" style="width:700px">
              <form class="" action="traitement.php" method="post" enctype="multipart/form-data"
              onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter le conmpte-rendu ?')">>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Date de la réunion</th>
                    <th style="width: 40px">L'Objet de la réunion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>
                          <select class="" name="select_date" required>
                            <?php
                                while ($row= $exe_requete->fetch(PDO::FETCH_ASSOC)) {
                                      //formatage de la date
                                    setlocale(LC_TIME,'fr_FR.UTF-8','fra');//Définit la l0calisati0n en francais
                                    $dateReunion= $row['date_reunion'];
                                    $DateTime= new DateTime($dateReunion);
                                    $jourSemaine=utf8_encode(strftime('%A',$DateTime->getTimestamp()));//%A p0ur le n0m c0mplet du j0ur de la semain
                                    $jour= $DateTime->format('d');
                                    $mois=utf8_encode(strftime('%B',$DateTime->getTimestamp())); //p0ur le n0m c0mplet du m0is
                                    $annee= $DateTime->format('Y');
                                          echo "<option value='".$row['id_reunion']."'>".$jourSemaine."  ".$jour."  ".$mois."  ".$annee."</option>";
                                    }

                             ?>
                          </select>
                    </td>

                    <td>
                      <select class="" name="select_objet">
                        <?php
                                while ($row= $exe->fetch(PDO::FETCH_ASSOC)) {
                                      echo "<option value='".$row['id_reunion']."'>".$row['ordre_du_jour']."</option>";
                                }
                         ?>
                      </select>
                    </td>
                  </tr>
                  <tr  style="background-color:#dddddd">
                    <td colspan="2">Selectionnez le fichier</td>

                    <td >
                          <input type="file" name="fichier_pdf" accept=".doc,.docx,.pdf" required>
                    </td>
                  </tr>
                </tbody>
              </table> <center>
              <input  class="bg-success" type="submit" name="bt_enreg" value="Enregistrer"> &nbsp;&nbsp;
              <input  class="bg-danger" type="reset" name="bt_annul" value="Annuler"> </center>
            </form>
            </div>
            <!-- /.card-body -->
        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  <?php
  }
  else {
    echo "<center><h2 class='bg-info'>Vous n'avez pas été sélectionné(e) pour rédiger un compte-rendu</h2></center>
      <center>
          <a href='javascript:history.go(-1)'class='bg-secondary'>Retour</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     </center>";
  }
   ?>
</div>
<!-- /.content-wrapper -->
