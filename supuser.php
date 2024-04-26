<?php
//on démarre la session PHP
session_start();

// Vérifier si la clé "user" existe dans la session et si l'utilisateur a le rôle admin
if (!(isset($_SESSION["user"]) && $_SESSION["user"]["role"] == "admin")) {
    header("Location: index.php");
    exit;
}

// On vérifie si on a un ID CAD on vérifie si $_GET["id_recette"] existe et n'est pas vide.
if (!isset($_GET["id_utilisateur"]) || empty($_GET["id_utilisateur"])) {
    // Je n'ai pas d'ID     Si $_GET["id"] n'existe pas ou est vide, on effectue une redirection vers "gereruser.php".
    header("Location: gereruser.php");
    exit;
}

// J'ai un ID, donc je le récupère
$id = $_GET["id_utilisateur"];

//on se co à la BDD
require_once "connect.php";

//on écrit la requete pour supprimer les favoris associés à l'utilisateur
$sqlDeleteFavorites = "DELETE FROM `favoris` WHERE id_utilisateur = :id_utilisateur";

//on prepare la requete
$queryDeleteFavorites = $db->prepare($sqlDeleteFavorites);
//on injecte les parametres
$queryDeleteFavorites->bindValue(":id_utilisateur", $id, PDO::PARAM_INT);
//on execute
$queryDeleteFavorites->execute();

//on écrit la requete pour supprimer les recettes associées à l'utilisateur
$sqlDeleteRecipe = "DELETE FROM `recettes` WHERE `id_auteur` = :id_utilisateur";

//on prepare la requete
$queryDeleteRecipe = $db->prepare($sqlDeleteRecipe);
//on injecte les parametres
$queryDeleteRecipe->bindValue(":id_utilisateur", $id, PDO::PARAM_INT);
//on execute
$queryDeleteRecipe->execute();

//on écrit la requete pour supprimer l'utilisateur
$sqlDeleteUser = "DELETE FROM `Utilisateurs` WHERE `id_utilisateur` = :id_utilisateur";

//on prepare la requete
$queryDeleteUser = $db->prepare($sqlDeleteUser);
//on injecte les parametres
$queryDeleteUser->bindValue(":id_utilisateur", $id, PDO::PARAM_INT);
//on execute
$queryDeleteUser->execute();

// redirection vers la liste des utilisateurs 
header("location: gereruser.php");
?>
