<?php
//Vérification des informations de connexion dans la base de données
$dsn= "mysql:host=localhost;dbname=gest_meet;charset=utf8";
$dbUsername="root";
$dbPassword= "";
  $con= new PDO($dsn,$dbUsername,$dbPassword);
  $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 ?>
