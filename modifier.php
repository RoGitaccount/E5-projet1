<?php
session_start();
require_once "connect.php"; // Inclure le fichier de connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$id_auteur = $_SESSION["user"]["id_auteur"];

// Vérifier si l'ID de la recette à modifier est présent dans l'URL
if (!isset($_GET['id_recette'])) {
    header("Location: MesRecettes.php"); // Rediriger vers la liste des recettes si l'ID de la recette n'est pas fourni
    exit;
}

$id_recette = $_GET['id_recette'];

// Récupérer les détails de la recette à modifier
$sql = "SELECT * FROM recettes WHERE id_recette = :id_recette AND id_auteur = :id_auteur";
$query = $db->prepare($sql);
$query->bindValue(':id_recette', $id_recette, PDO::PARAM_INT);
$query->bindValue(':id_auteur', $id_auteur, PDO::PARAM_INT);
$query->execute();

// Vérifier si la recette appartient à l'utilisateur
if ($query->rowCount() === 0) {
    header("Location: MesRecettes.php"); // Rediriger vers la liste des recettes si la recette n'appartient pas à l'utilisateur
    exit;
}

$recette = $query->fetch(PDO::FETCH_ASSOC);

// Mettre à jour la recette
if (isset($_POST['modifier_recette'])) {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $etapes_preparation = $_POST['etapes_preparation'];
    $temps_preparation = $_POST['temps_preparation'];
    $temps_cuisson = $_POST['temps_cuisson'];

    // Vérifier si une nouvelle image a été téléchargée
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Valider le type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if(in_array($_FILES['image']['type'], $allowedTypes)) {
            // Générer un nom unique pour le fichier téléchargé
            $newImageName = "rcp" . md5(uniqid()) . '_' . $_FILES['image']['name'];
            // Déplacer le fichier téléchargé vers un dossier d'images sur le serveur
            $uploadPath = 'images/' . $newImageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);

            // Supprimer l'ancienne image si elle n'est pas l'image par défaut
            if ($recette['image'] !== 'img_defaut.png') {
                unlink('images/' . $recette['image']);
            }

            // Mettre à jour le nom de l'image dans la base de données
            $sqlUpdateImage = "UPDATE recettes SET image = :image WHERE id_recette = :id_recette";
            $queryUpdateImage = $db->prepare($sqlUpdateImage);
            $queryUpdateImage->bindValue(':image', $newImageName, PDO::PARAM_STR);
            $queryUpdateImage->bindValue(':id_recette', $id_recette, PDO::PARAM_INT);
            $queryUpdateImage->execute();
        } else {
            // Gérer le cas où le type de fichier n'est pas autorisé
            $_SESSION["error"][] = "Seuls les fichiers JPEG, PNG et JPG sont autorisés.";
        }
    }

    // Mettre à jour les autres champs de la recette dans la base de données
    $sql = "UPDATE recettes SET titre = :titre, description = :description, ingredients = :ingredients, 
            etapes_preparation = :etapes_preparation, temps_preparation = :temps_preparation, 
            temps_cuisson = :temps_cuisson WHERE id_recette = :id_recette";
    $query = $db->prepare($sql);
    $query->bindValue(':titre', $titre, PDO::PARAM_STR);
    $query->bindValue(':description', $description, PDO::PARAM_STR);
    $query->bindValue(':ingredients', $ingredients, PDO::PARAM_STR);
    $query->bindValue(':etapes_preparation', $etapes_preparation, PDO::PARAM_STR);
    $query->bindValue(':temps_preparation', $temps_preparation, PDO::PARAM_STR);
    $query->bindValue(':temps_cuisson', $temps_cuisson, PDO::PARAM_STR);
    $query->bindValue(':id_recette', $id_recette, PDO::PARAM_INT);
    $query->execute();

    header("Location: MesRecettes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Metas -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="author" content="" />
    <link rel="icon" href="icon/favicon.png" type="image/gif" />

    <title>Modifier la Recette</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />
</head>
<body class="sub_page">

<!-- Header -->
<header class="header_section long_section px-0">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom_nav-container">
        <a class="navbar-brand" href="index.php">
            <span>ShareRecipe</span>
        </a>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse mx-auto" id="navbarSupportedContent">
            <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Listes_recettes.php">Listes des recettes</a>
                    </li>
                    <?php if(isset($_SESSION["user"])): ?>
                        <?php if($_SESSION["user"]["role"] == "utilisateur" || $_SESSION["user"]["role"]== "admin"): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="MesRecettes.php">Mes recettes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="Mesfav.php">Mes favoris</a>
                            </li>
                        <?php endif; ?>
                        <?php if($_SESSION["user"]["role"] == "admin"): ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="contact.html">Gérer les utilisateurs</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <!-- User actions -->
                <?php if(!isset($_SESSION["user"])): ?>
                    <div class="quote_btn-container">
                        <a href="inscription.php">
                            <span>Inscription</span>
                        </a>
                    </div>
                    <div class="quote_btn-container">
                        <a href="connexion.php">
                            <span>Connexion</span>
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="quote_btn-container">
                        <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                        <a href="profils.php">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span><?=strip_tags($_SESSION["user"]["prenom"]) ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<!-- Main content -->
<div class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-md shadow-md">
        <h1 class="text-2xl font-bold mb-4">Modifier la Recette</h1>
        <!-- Formulaire pour modifier une recette -->
        <form method="post" enctype="multipart/form-data">

        <div>
            <!-- Image de la recette -->
            <img src="images/<?= $recette['image'] ?>" alt="image de la recette" id="profile-pic" style="width: 180px; height: 180px;" class="mb-4 rounded-full">

            <!-- Bouton pour ajouter une image -->
            <button type="button" onclick="document.getElementById('input-file').click()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Modifier mon image</button>

            <!-- Champ caché pour télécharger l'image -->
            <input type="file" name="image" id="input-file" style="display: none;">
        </div>
        <br>
            <div class="mb-4">
                <label for="titre" class="block mb-2">Titre :</label>
                <input type="text" id="titre" name="titre" value="<?= $recette['titre'] ?>" required
                       class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full">
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-2">Description :</label>
                <textarea id="description" name="description" required
                          class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full"><?= $recette['description'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="ingredients" class="block mb-2">Ingrédients :</label>
                <textarea id="ingredients" name="ingredients" required
                          class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full"><?= $recette['ingredients'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="etapes_preparation" class="block mb-2">Étapes de préparation :</label>
                <textarea id="etapes_preparation" name="etapes_preparation" required
                          class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full"><?= $recette['etapes_preparation'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="temps_preparation" class="block mb-2">Temps de préparation :</label>
                <select id="temps_preparation" name="temps_preparation" required
                        class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full">
                    <!-- Options de temps de préparation -->
                    <option value="<?= $recette['temps_preparation'] ?>"><?= $recette['temps_preparation'] ?></option>
                    <option value="Non renseigné">Non renseigné</option>
                    <option value="moins de 15 minutes">moins de 15 minutes</option>
                    <option value="15 minutes">15 minutes</option>
                    <option value="30 minutes">30 minutes</option>
                    <option value="1 heure">1 heure</option>
                    <option value="1 heure et 30 minutes">1 heure et 30 minutes</option>
                    <option value="2 heures">2 heures</option>
                    <option value="plus de 2 heures">plus de 2 heures</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="temps_cuisson" class="block mb-2">Temps de cuisson :</label>
                <select id="temps_cuisson" name="temps_cuisson" required
                        class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 w-full">
                    <!-- Options de temps de cuisson -->
                    <option value="<?= $recette['temps_cuisson'] ?>"><?= $recette['temps_cuisson'] ?></option>
                    <option value="non renseigné">Non renseigné</option>
                    <option value="moins de 15 minutes">moins de 15 minutes</option>
                    <option value="15 minutes">15 minutes</option>
                    <option value="30 minutes">30 minutes</option>
                    <option value="1 heure">1 heure</option>
                    <option value="1 heure et 30 minutes">1 heure et 30 minutes</option>
                    <option value="2 heures">2 heures</option>
                    <option value="plus de 2 heures">plus de 2 heures</option>
                </select>
            </div>

            <button type="submit" name="modifier_recette"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Modifier la Recette</button>
        </form>
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
