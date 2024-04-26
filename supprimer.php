<?php
// On démarre la session PHP
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Vérifier si $_GET["id_recette"] existe et n'est pas vide.
if (!isset($_GET["id_recette"]) || empty($_GET["id_recette"])) {
    // Redirection vers "MesRecettes.php" si l'ID n'est pas valide
    header("Location: MesRecettes.php");
    exit;
}

// Récupérer l'ID de la recette à supprimer
$id = $_GET["id_recette"];

// Inclure le fichier de connexion à la base de données
require_once "connect.php";

// Récupérer le nom du fichier image associé à la recette
$sqlGetImage = "SELECT image FROM recettes WHERE id_recette = :id_recette";
$queryGetImage = $db->prepare($sqlGetImage);
$queryGetImage->bindValue(":id_recette", $id, PDO::PARAM_INT);
$queryGetImage->execute();
$imageData = $queryGetImage->fetch(PDO::FETCH_ASSOC);

// Si une image est associée à la recette et si ce n'est pas l'image par défaut, supprimer le fichier du dossier "images"
if ($imageData && $imageData['image'] !== 'img_defaut.jpg') {
    // Vérifier si le nom de l'image est différent de l'image par défaut
    $defaultImage = 'img_defaut.jpg';
    if ($imageData['image'] !== $defaultImage) {
        $filePath = "images/" . $imageData['image'];
        // Vérifier si le fichier existe avant de le supprimer
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}



// Supprimer les favoris associés à la recette
$sqlDeleteFavorites = "DELETE FROM favoris WHERE id_recette = :id_recette";
$queryDeleteFavorites = $db->prepare($sqlDeleteFavorites);
$queryDeleteFavorites->bindValue(":id_recette", $id, PDO::PARAM_INT);
$queryDeleteFavorites->execute();

// Supprimer la recette de la base de données
$sqlDeleteRecipe = "DELETE FROM recettes WHERE id_recette = :id_recette";
$queryDeleteRecipe = $db->prepare($sqlDeleteRecipe);
$queryDeleteRecipe->bindValue(":id_recette", $id, PDO::PARAM_INT);
$queryDeleteRecipe->execute();

// Redirection vers MesRecettes.php
header("Location: MesRecettes.php");
exit;
?>
