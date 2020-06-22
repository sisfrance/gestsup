<?php
$serveur="localhost:3306";//nom du serveur
$user="root";//votre nom utilisateur
$password="";//mot de passe
$base="tickets";//nom de la base de donnée
$connexion = mysql_connect($serveur,$user,$password) or die("impossible de se connecter : ". mysql_error());
$db = mysql_select_db($base, $connexion)  or die("impossible de sélectionner la base : ". mysql_error());
?>