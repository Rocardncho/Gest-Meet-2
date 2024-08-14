<?php
if (isset($_POST["directions_id"]) && !empty($_POST["directions_id"])) {
    try {
        require_once "../../connexion.php";

        $directions_id = $_POST["directions_id"];
        $id_poste_selected = isset($_POST['id_poste']) ? $_POST['id_poste'] : 0;

        // Ajout d'un message de débogage pour vérifier les valeurs reçues
        error_log("Directions ID reçu: " . $directions_id);
        error_log("ID poste sélectionné: " . $id_poste_selected);

        $stmt = $con->prepare("SELECT * FROM postes
                               WHERE is_deleted = FALSE AND
                               directions_id = ?");
        $stmt->execute([$directions_id]);

        if ($stmt->rowCount() > 0) {
            $html = '<option value="" selected disabled>--Sélectionner le poste--</option>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($row['id_poste'] == $id_poste_selected) ? 'selected' : '';
                $html .= "<option value='".$row['id_poste']."' $selected>".$row['libelle_poste']."</option>";
            }
            echo $html;
        } else {
            echo '<option value="">Pas de poste disponible, veuillez en ajouter.</option>';
        }
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}elseif (isset($_POST["directions_id"]) && empty($_POST["directions_id"])) {
  // code...
  try {
      require_once "../../connexion.php";

      $directions_id = 0;
      $id_poste_selected = isset($_POST['id_poste']) ? $_POST['id_poste'] : 0;

      // Ajout d'un message de débogage pour vérifier les valeurs reçues
      error_log("Directions ID reçu: " . $directions_id);
      error_log("ID poste sélectionné: " . $id_poste_selected);

      $stmt = $con->prepare("SELECT * FROM postes
                             WHERE is_deleted = FALSE AND
                             directions_id = ?");
      $stmt->execute([$directions_id]);

      if ($stmt->rowCount() > 0) {
          $html = '<option value="" selected disabled>--Sélectionner le poste--</option>';
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $selected = ($row['id_poste'] == $id_poste_selected) ? 'selected' : '';
              $html .= "<option value='".$row['id_poste']."' $selected>".$row['libelle_poste']."</option>";
          }
          echo $html;
      } else {
          echo '<option value="">Pas de poste disponible, veuillez en ajouter.</option>';
      }
  } catch (PDOException $e) {
      echo "Erreur: " . $e->getMessage();
  }
}
 else {
    // Ajout d'un message de débogage pour le cas où directions_id est vide ou non défini
    error_log("Directions ID est vide ou non défini.");
}
?>
