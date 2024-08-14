<?php
  session_start();
if ($_SERVER['REQUEST_METHOD']==='POST')
  {
    //recuperation des données du formulaire
    //var_dump($_POST);
    $username= $_POST['username'];
    $password= $_POST['password'];
    //Validation des infprmations de connexion
    if(validerConnexion($username,$password))
        {
          $_SESSION['loggedin'] = true;
          $_SESSION['username']=$username;
          header('Location:tableau-de-bord.php');
          exit();
        }
    }
  function validerConnexion($username, $password)
    {
      //inclure de la page de connexion à la BDD
      require_once"connexion.php";

        try {
          // Récupération du mot de passe haché depuis la base de données
        $query= "SELECT mot_de_passe FROM utilisateurs WHERE mail=:username
                             AND is_deleted=FALSE";
        //preparons la requete pour eviter les attaques par injection
        $stmt= $con->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $hashed_password = $result['mot_de_passe'];
                // Vérification du mot de passe
                 if (password_verify($password, $hashed_password)) {
                   return true;
                 }else {
                          echo "<center><h4 style='height:30px;background-color:#ff9999;'>Mot de passe incorrect !</h4></center>
                              <br/><br/><center> <a href='/GEST-MEET/'>
                                 <span style='size:50px;'>Réesayez</span></a></center> ";
                      }
              }else {
                        echo "<center><h4 style='height:30px;background-color:#ff9999;'>Adresse email non trouvée</h4></center>
                            <br/><br/><center> <a href='/GEST-MEET/'>
                               <span style='size:50px;'>Réesayez</span></a></center> ";
                   }
      }catch(PDOException $e){
        echo "Erreur de connexion à la base de données:".$e->getMessage();;
        return false;
      }
    }
 ?>
