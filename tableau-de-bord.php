<?php
session_start();
// Si l'utilisateur ne s'est authentifié avant de venir ici
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /GEST-MEET/");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestion des Réunions</title>
  <!-- CSS de DataTables -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/style.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body  class="hold-transition sidebar-mini layout-fixed">
  <!-- Preloader -->
  <?php
    $username = $_SESSION['username'];
    require_once "connexion.php";
    // DATE ET HEURE
    $dateTime = new DateTime(); // Crée un objet DateTime avec la date et l'heure actuelles
    $dateTimeFormat=$dateTime->format('Y-m-d H:i:s'); // Affiche la date et l'heure au format spécifié
    // DATE
    $date = new DateTime();
    $dateFormat = $date->format('Y-m-d');
    $heure = new DateTime();
    $heureFormat = $heure->format('H:i:s');

    // SÉLECTION DU NOM DE LA DIRECTION DE L'UTILISATEUR CONNECTé ET ID
    $requete = "SELECT d.id_direction, d.libelle_direction
                FROM directions AS d
                INNER JOIN postes AS p ON p.directions_id = d.id_direction
                INNER JOIN utilisateurs AS u ON p.id_poste = u.poste_id
                WHERE u.mail = :username AND d.id_direction<>0 AND u.is_deleted = FALSE";
    $stmt = $con->prepare($requete);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      $directeurConnect = false;
      $rowId = $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
        $directeurConnect = true;
      }
     ?>
  <div class="wrapper" >
    <!-- Preloader -->
    <?php
// <div class="preloader flex-column justify-content-center align-items-center">
  // <img class="animation__shake" src="dist/img/ordi.png" alt="GEST-MEET" height="200" width="200">
// </div>
     ?>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="tableau-de-bord.php" class="nav-link">ACCUEIL</a>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <?php
            // Total des Nouvelle notifications
            if (!$directeurConnect) {
            $requete = "SELECT COUNT(*) AS total FROM notifications AS n
                      INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
                      INNER JOIN directions AS d  ON d.id_direction=r.directions_id
            WHERE n.date_notification= '$dateFormat' AND
                  (d.libelle_direction='".$rowId['libelle_direction']."'
                      OR d.id_direction=0)
                    ";
              }else {
                $requete = "SELECT COUNT(*) AS total FROM notifications AS n
                          INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
                          INNER JOIN directions AS d  ON d.id_direction=r.directions_id
                WHERE n.date_notification= '$dateFormat'
                        ";
              }
            $exe_requete = $con->query($requete);
            $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
            if ($row['total'] > 0){ ?>
            <span class="badge badge-warning navbar-badge"><?php echo $row['total']; ?></span>
          <?php } ?>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header"><?php echo $row['total']; ?> Notification(s)</span>
            <div class="dropdown-divider"></div>
            <?php
            $parametre2 = 'Nouvelle-reunion';
            $url = 'reunions.php?parametre2=' . urlencode($parametre2);
            ?>
            <a href="<?php echo $url ?>" class="dropdown-item">
              <?php
              //Nouvelles reunions  programmées
              if (!$directeurConnect) {
              $requete = "SELECT COUNT(*) AS total FROM notifications AS n
              INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
              INNER JOIN directions AS d  ON d.id_direction=r.directions_id
              WHERE (d.libelle_direction='".$rowId['libelle_direction']."'
                    OR d.id_direction=0) AND
                    contenu_notification LIKE 'Une reunion%'
                     AND date_notification='$dateFormat'";
                  }else {
                    $requete = "SELECT COUNT(*) AS total FROM notifications AS n
                    INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
                    INNER JOIN directions AS d  ON d.id_direction=r.directions_id
                    WHERE  contenu_notification LIKE 'Une reunion%'
                           AND date_notification='$dateFormat'";
                  }
              $exe_requete = $con->query($requete);
              if ($exe_requete) {
                $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
              ?>
              <i class="fas fa-users mr-2"></i> <?php echo $row['total'] . " Nouvelle(s) réunion(s)";
              }
              ?>
            </a>
            <div class="dropdown-divider"></div>
            <?php
            $parametre = 'nouveau-compte-rendu';
            $url = 'liste-des-comptes-rendus.php?parametre=' . urlencode($parametre);
            ?>
            <a href="<?php echo $url ?>" class="dropdown-item">
              <?php
            if (!$directeurConnect) {
              $requete = "SELECT COUNT(*) AS total FROM notifications AS n
              INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
              INNER JOIN directions AS d  ON d.id_direction=r.directions_id
               WHERE   (d.libelle_direction='".$rowId['libelle_direction']."'
                          OR d.id_direction=0)
                      AND contenu_notification LIKE'Un compte-rendu%'
                      AND date_notification = '$dateFormat'";
                }else {
                  $requete = "SELECT COUNT(*) AS total FROM notifications AS n
                  INNER JOIN reunions AS r ON r.id_reunion=n.reunion_id
                  INNER JOIN directions AS d  ON d.id_direction=r.directions_id
                   WHERE contenu_notification LIKE'Un compte-rendu%'
                          AND date_notification = '$dateFormat'";
                }
              $exe_requete = $con->query($requete);
              $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
              ?>
              <i class="fas fa-file mr-2"></i> <?php echo $row['total']; ?> nouveau(x) compte-rendu(s)
            </a>
            <div class="dropdown-divider"></div>
            <a href="notifications.php" class="dropdown-item dropdown-footer">Voir toutes les Notifications</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="https://agenceemploijeunes.ci/site/bienvenue" class="brand-link">
        <img src="dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Site internet</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="dist/img/avatar.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block">
              <?php
              try {
                $requete = "SELECT * FROM utilisateurs WHERE mail='" . $username . "'";
                $exe_requete = $con->query($requete);
                $row = $exe_requete->fetch(PDO::FETCH_ASSOC);
                $nom = $row['nom'];
                $prenom = $row['prenom'];
                echo "$nom $prenom";
              } catch (\Exception $e) {
                echo "Le nom ou le prénom n'existe pas dans la base de données";
              }
              ?>
            </a>
          </div>
        </div>
            <?php
                    $progReunRole=false;
                    $adminRole=false;
             ?>
        <!-- SidebarSearch Form -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Recherche" aria-label="Recherche">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

    <?php
    //Gestion des autorisations
    $requete = "SELECT u.*, r.* FROM utilisateurs AS u
     INNER JOIN  UtilisateursRoles AS r ON
      u.matricule=r.matricule_id
       WHERE u.mail='$username' AND u.is_deleted=FALSE";
    $exe_requete = $con->query($requete);   ?>

     <!-- Sidebar Menu -->
<nav class="mt-2">
   <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
       <?php
       // Initialisation des variables de rôle
       $roles = [];

       // Récupérer et traiter chaque ligne
       while ($row = $exe_requete->fetch(PDO::FETCH_ASSOC)) {
           $roles[] = $row['role_id'];
       }

       // Supprimer les doublons
       $roles = array_unique($roles);

       // Menu Compte rendus
       if (in_array(2, $roles) || in_array(3, $roles)) { ?>
           <li class="nav-item menu">
               <a href="#" class="nav-link active">
                   <i class="nav-icon fas fa-tachometer-alt"></i>
                   <p>
                       Comptes-rendus
                       <i class="right fas fa-angle-left"></i>
                   </p>
               </a>
               <ul class="nav nav-treeview">
                   <?php
                   // Cas où l'utilisateur peut voir les comptes-rendus
                   if (in_array(2, $roles)) {  ?>
                       <li class="nav-item">
                           <a href="liste-des-comptes-rendus.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Liste des comptes-rendus</p>
                           </a>
                       </li>
                   <?php }

                   // Cas où l'utilisateur peut ajouter des comptes-rendus
                   if (in_array(3, $roles)) { ?>
                       <li class="nav-item">
                           <a href="ajout-de-comptes-rendus.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Ajoutez un compte-rendu</p>
                           </a>
                       </li>
                   <?php } ?>
               </ul>
           </li>
       <?php } ?>

       <!-- Menu Programme-->
           <li class="nav-item menu">
               <a href="#" class="nav-link active">
                   <i class="nav-icon fas fa-th"></i>
                   <p>
                       Programme <i class="right fas fa-angle-left"></i>
                   </p>
               </a>
               <ul class="nav nav-treeview">
              <?php if (in_array(5, $roles)) { $progReunRole=true; ?>
                   <li class="nav-item">
                       <a href="programmer-reunion.php" class="nav-link">
                           <i class="far fa-circle nav-icon"></i>
                           <p>Programmer une réunion</p>
                       </a>
                   </li>
                 <?php } ?>
                   <li class="nav-item">
                       <a href="reunions.php" class="nav-link">
                           <i class="far fa-circle nav-icon"></i>
                           <p>Liste des réunions</p>
                       </a>
                   </li>
               </ul>
           </li>

       <?php
       // Menu utilisateurs
       if (in_array(6, $roles) || in_array(10, $roles) || in_array(11, $roles) || in_array(12, $roles) || in_array(8, $roles) || in_array(9, $roles)) {
          if (in_array(6, $roles) && in_array(8, $roles) && in_array(12, $roles)){
           $adminRole=true;
          } ?>
           <li class="nav-item">
               <a href="#" class="nav-link active">
                   <i class="nav-icon fas fa-copy"></i>
                   <p>
                       Utilisateurs <i class="right fas fa-angle-left"></i>
                   </p>
               </a>
               <ul class="nav nav-treeview">
                   <?php
                   // Cas où l'utilisateur peut ajouter un utilisateur
                   if (in_array(11, $roles) || in_array(12, $roles)) { ?>
                       <li class="nav-item">
                           <a href="ajout-utilisateur.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Ajoutez un utilisateur</p>
                           </a>
                       </li>
                   <?php }

                   // Cas où l'utilisateur peut ajouter des postes et directions
                   if (in_array(6, $roles) || in_array(10, $roles)) {
                       if ($directeurConnect || $adminRole) { ?>
                       <li class="nav-item">
                           <a href="ajout-direction.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Ajoutez une sous-direction</p>
                           </a>
                       </li>
                     <?php } ?>
                       <li class="nav-item">
                           <a href="ajout-poste.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Ajoutez un poste</p>
                           </a>
                       </li>
                   <?php }

                   // Cas où l'utilisateur peut voir la liste des utilisateurs
                   if (in_array(8, $roles) || in_array(9, $roles)) { ?>
                       <li class="nav-item">
                           <a href="liste-des-utilisateurs.php" class="nav-link">
                               <i class="far fa-circle nav-icon"></i>
                               <p>Liste des utilisateurs</p>
                           </a>
                       </li>
                   <?php } ?>
               </ul>
           </li>
       <?php } ?>
       <li class="nav-item">
           <a href="/GEST-MEET/lagout.php" class="nav-link active"
           onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');"
           >
               <i class="far fa-circle nav-icon"></i>
               <p>Déconnexion</p>
           </a>
       </li>
   </ul>
</nav>
<!-- /.sidebar-menu -->

      </div>
      <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- content -->
      <?php
      require_once "contenu.php";
      ?>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->


<!-- JavaScript de jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- JavaScript de DataTables -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "pageLength": 5, // Nombre de lignes par page
            "lengthChange": false, // Cache le menu de sélection du nombre de lignes
            "language": {
                "paginate": {
                    "previous": "Précédent",
                    "next": "Suivant"
                },
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ réunions"
            }
        });
    });
</script>
  <!-- sert à inclure la bibliothèque jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- .script jQuery pour effectuer une requête AJAX -->
<script>
$(document).ready(function(){
    var directionID = '<?php echo $selectedDirectionID; ?>';
    var posteID = '<?php echo $selectedPosteID; ?>';  // Définir la valeur par défaut à 0 si undefined

    console.log("Direction ID initial: " + directionID);
    console.log("Poste ID initial: " + posteID);

    $('#select_direct').on('change', function(){
        directionID = $(this).val();
        console.log("Direction ID changé: " + directionID);
        loadPostes(directionID, posteID);
    });

    if (directionID) {
        $('#select_direct').val(directionID);
        loadPostes(directionID, posteID);
    }

    function loadPostes(directionID, posteID){
        if (directionID) {
            console.log("Appel AJAX avec Direction ID: " + directionID + " et Poste ID: " + posteID);
            $.ajax({
                type: 'POST',
                url: 'dist/AJAX/ajaxData.php',
                data: {
                    directions_id: directionID,
                    id_poste: posteID
                },
                success: function(html){
                    console.log("Réponse AJAX: " + html);
                    $('#select_poste').html(html);
                }
            });
        } else {
            $('#select_poste').html('<option value="">Sélectionner la direction d\'abord</option>');
        }
    }
});
</script>
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4-->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!--  -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
</body>
</html>
