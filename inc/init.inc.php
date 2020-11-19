<?php 

// CONNEXION BDD
$bdd = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// $bdd = new PDO('mysql:host=sql200.epizy.com;dbname=epiz_27185175_boutique', 'epiz_27185175', 'xCy28Khbtpyu2', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// SESSION
session_start();

// CONSTANTE (chemin)
define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . '/PHP/09-boutique/');

// define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . '/');

// $_SERVER['DOCUMENT_ROOT'] --> c:/xampp/htdocs
// echo RACINE_SITE . '<hr>'; // c:/xampp/htdocs/PHP/9-boutique/

// Cette constante retourne le chemin physique di dossier 9-boutique sur le serveur local xampp.
// Lors de l'enregistrement d'une image/photo, nous aurons du chemin physique complet vers le dossier photo sur le serveur pour enregistrer la photo dans le bon dossier
// On appel $_SERVER['DOCUMENT_ROOT'] parce que chaque serveur possède des chemins diffdérents 

define("URL", "http://localhost/PHP/09-boutique/");

// define("URL", "http://ecommerce-cours.rf.gd/");

// cette constante servira à enregistrer l'URL d'une image/photo  dans la BDD

// INCLUSIONS 
// En appelant init.inc sur chaque fichier, nous incluons en même temps les fonctions déclarées
require_once('fonctions.inc.php');