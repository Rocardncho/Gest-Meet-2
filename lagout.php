<?php
// Destructions des sessions après la déconnexion de l'utilisateur
session_start();
session_unset();
session_destroy();
header("Location: /GEST-MEET/");
?>
