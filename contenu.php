<?php
//............................Page ajout direction................................
if (basename($_SERVER['SCRIPT_NAME'])=='ajout-direction.php')
      {
          require_once"contenus/contenu-ajout-direction.php";
      }

//............................Page liste utilisateurs................................
if (basename($_SERVER['SCRIPT_NAME'])=='liste-des-utilisateurs.php')
      {
          require_once"contenus/contenu-liste-des-utilisateurs.php";
      }
//............................Page Ajout,poste................................
if (basename($_SERVER['SCRIPT_NAME'])=='ajout-poste.php')
      {
          require_once"contenus/contenu-ajout-poste.php";
      }
//............................Page reunions................................
if (basename($_SERVER['SCRIPT_NAME'])=='reunions.php')
      {
          require_once"contenus/contenu-reunions.php";
      }
//............................Page de notifications................................
if (basename($_SERVER['SCRIPT_NAME'])=='notifications.php')
      {
          require_once"contenus/contenu-notifications.php";
      }
//............................Page de programmati0n de reunion...................................
if (basename($_SERVER['SCRIPT_NAME'])=='ajout-utilisateur.php')
      {
          require_once"contenus/contenu-ajout-utilisateur.php";
      }
//............................Page de programmati0n de reunion...................................
if (basename($_SERVER['SCRIPT_NAME'])=='programmer-reunion.php')
      {
          require_once"contenus/contenu-programmer-reunion.php";
      }
//............................Page de traitement...................................
  if (basename($_SERVER['SCRIPT_NAME'])=='traitement.php')
        {
            require_once"contenus/contenu-traitement.php";
        }

//............................Page Ajout de compte...................................
  if (basename($_SERVER['SCRIPT_NAME'])=='ajout-de-comptes-rendus.php')
        {
            require_once"contenus/contenu-ajout-de-comptes-rendus.php";
        }

//............................Page liste...................................
  if (basename($_SERVER['SCRIPT_NAME'])=='liste-des-comptes-rendus.php')
        {
            require_once"contenus/contenu-liste-comptes-rendus.php";
        }

//............................Page d'acceuil....................................
if (basename($_SERVER['SCRIPT_NAME'])=='tableau-de-bord.php')
        {
                  require_once"contenus/contenu-accueil.php";
        }
  ?>
