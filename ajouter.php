<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$id_auteur = $_SESSION["user"]["id_auteur"];

if (!empty($_POST)) {
    // Vérification des champs requis
    $required_fields = ["titre", "description", "ingredients", "etapes_preparation", "temps_preparation", "temps_cuisson"];
    $form_complete = true;
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $form_complete = false;
            $_SESSION["error"][] = "Le champ $field est requis";
        }
    }

    // Vérifier si une recette avec le même titre existe déjà pour l'utilisateur connecté
    if ($form_complete) {
        require_once "connect.php"; // Inclure le fichier de connexion à la base de données
        
        $titre = strip_tags($_POST['titre']); // Récupérer le titre sans balises HTML

        $sql_check_title = "SELECT * FROM recettes WHERE titre = :titre AND id_auteur = :id_auteur";
        $query_check_title = $db->prepare($sql_check_title);
        $query_check_title->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query_check_title->bindValue(':id_auteur', $id_auteur, PDO::PARAM_INT);
        $query_check_title->execute();
        $existing_recipes = $query_check_title->fetchAll(PDO::FETCH_ASSOC);

        if ($existing_recipes) {
            $_SESSION["error"][] = "Une recette avec ce titre existe déjà pour cet utilisateur.";
            $form_complete = false; // Marquer le formulaire comme incomplet
        }
    }

    // Créer une nouvelle recette si le formulaire est complet et qu'il n'y a pas de recette avec le même titre
    if ($form_complete) {
        $description = strip_tags($_POST['description']);
        $ingredients = strip_tags($_POST['ingredients']);
        $etapes_preparation = strip_tags($_POST['etapes_preparation']);
        $temps_preparation = strip_tags($_POST['temps_preparation']);
        $temps_cuisson = strip_tags($_POST['temps_cuisson']);
        $_SESSION["error"] = [];

         // Initialiser le nom de l'image à "img_defaut.jpg" par défaut
    $imageName = 'img_defaut.jpg';

    // Traitement de l'image téléchargée si elle est fournie
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Valider le type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if(in_array($_FILES['image']['type'], $allowedTypes)) {
            // Générer un nom unique pour le fichier téléchargé
            $imageName = "rcp" . md5(uniqid()) . '_' . $_FILES['image']['name'];
            // Déplacer le fichier téléchargé vers un dossier d'images sur le serveur
            $uploadPath = 'images/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        } else {
            $_SESSION["error"][] = "Seuls les fichiers JPEG, PNG et JPG sont autorisés.";
        }
    }

        if (empty($_SESSION["error"])) {
            // Ajouter la recette à la base de données
            $sql = "INSERT INTO recettes (titre, description, ingredients, etapes_preparation, temps_preparation, temps_cuisson, image, id_auteur, date_creation) 
            VALUES (:titre, :description, :ingredients, :etapes_preparation, :temps_preparation, :temps_cuisson, :image, :id_auteur, NOW())";
            $query = $db->prepare($sql);
            $query->bindValue(':titre', $titre, PDO::PARAM_STR);
            $query->bindValue(':description', $description, PDO::PARAM_STR);
            $query->bindValue(':ingredients', $ingredients, PDO::PARAM_STR);
            $query->bindValue(':etapes_preparation', $etapes_preparation, PDO::PARAM_STR);
            $query->bindValue(':temps_preparation', $temps_preparation, PDO::PARAM_STR);
            $query->bindValue(':temps_cuisson', $temps_cuisson, PDO::PARAM_STR);
            $query->bindValue(':image', $imageName, PDO::PARAM_STR); // Utiliser le nom de l'image
            $query->bindValue(':id_auteur', $id_auteur, PDO::PARAM_INT);
            $query->execute();

            // Récupérer l'ID de la recette nouvellement insérée
            $id_recette = $db->lastInsertId();

            header("Location: MesRecettes.php");
            exit;
        } else {
            $_SESSION["error"][] = "L'ajout de la recette n'a pas pu aboutir";
        }
    }else{$_SESSION["error"][] = "La recette est incomplète";}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Recettes</title>
    <!-- Ajout de Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

<div class="container mx-auto py-6">
    <?php
    // Affichage des erreurs
    if (isset($_SESSION["error"])) {
        foreach ($_SESSION["error"] as $erreur) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded' role='alert'>";
            echo "$erreur";
            echo "</div>";
        }
        unset($_SESSION["error"]);
    }
    ?>

    <!-- Formulaire pour ajouter une nouvelle recette -->
    <form class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg" method="post" enctype="multipart/form-data">
    <h1 class="text-2xl font-bold mb-4">Ajouter une Recette</h1> 
        <div>
            <!-- Image par défaut -->
            <img src="images/img_defaut.jpg" alt="image par défaut" id="profile-pic" style="width: 180px; height: 180px;" class="mb-4 rounded-full">

            <!-- Bouton pour ajouter une image -->
            <button type="button" onclick="document.getElementById('input-file').click()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Ajouter une image</button>

            <!-- Champ caché pour télécharger l'image -->
            <input type="file" name="image" id="input-file" style="display: none;">
        </div>
    <br>
        <label for="titre" class="block text-sm font-medium text-gray-700">Titre :</label>
        <input type="text" id="titre" name="titre" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md">

        <label for="description" class="block mt-4 text-sm font-medium text-gray-700">Description :</label>
        <textarea id="description" name="description" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md"></textarea>

        <label for="ingredients" class="block mt-4 text-sm font-medium text-gray-700">Ingrédients :</label>
        <textarea id="ingredients" name="ingredients" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md"></textarea>

        <label for="etapes_preparation" class="block mt-4 text-sm font-medium text-gray-700">Étapes de préparation :</label>
        <textarea id="etapes_preparation" name="etapes_preparation" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md"></textarea>

        <label for="temps_preparation" class="block mt-4 text-sm font-medium text-gray-700">Temps de préparation :</label>
        <select id="temps_preparation" name="temps_preparation" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md">
            <option value="Non renseigné">Non renseigné</option>
            <option value="moins de 15 minutes">moins de 15 minutes</option>
            <option value="15 minutes">15 minutes</option>
            <option value="30 minutes">30 minutes</option>
            <option value="1 heure">1 heure</option>
            <option value="1 heure et 30 minutes">1 heure et 30 minutes</option>
            <option value="2 heures">2 heures</option>
            <option value="plus de 2 heures">plus de 2 heures</option>
        </select>

        <label for="temps_cuisson" class="block mt-4 text-sm font-medium text-gray-700">Temps de cuisson :</label>
        <select id="temps_cuisson" name="temps_cuisson" required class="mt-1 p-2 block w-full border border-gray-300 rounded-md">
            <option value="non renseigné">Non renseigné</option>
            <option value="moins de 15 minutes">moins de 15 minutes</option>
            <option value="15 minutes">15 minutes</option>
            <option value="30 minutes">30 minutes</option>
            <option value="1 heure">1 heure</option>
            <option value="1 heure et 30 minutes">1 heure et 30 minutes</option>
            <option value="2 heures">2 heures</option>
            <option value="plus de 2 heures">plus de 2 heures</option>
        </select>

        <div class="flex justify-end mt-4">
            <a href="MesRecettes.php" class="text-blue-500 hover:text-blue-700 font-medium">Retour</a>
            <button type="submit" class="ml-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Ajouter une Recette</button>
        </div>
    </form>
     <!-- Script pour afficher l'image sélectionnée -->
     <script>
        let profilePic = document.getElementById("profile-pic");
        let inputFile = document.getElementById("input-file");

        // Lorsque le fichier est sélectionné, mettez à jour l'aperçu de l'image
        inputFile.onchange = function () {
            profilePic.src = URL.createObjectURL(inputFile.files[0]);
        }
    </script>
</div>
</body>
</html>
