<?php
session_start();

require_once "connect.php";

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Traitement de la soumission du formulaire pour retirer des favoris
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_favoris"])) {
    // Retirer des favoris
    $id_utilisateur = $_SESSION["user"]["id_auteur"];
    $id_recette = $_POST["id_recette"];
    $sql = "DELETE FROM Favoris WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";

    // Exécution de la requête SQL
    $stmt = $db->prepare($sql);
    $stmt->execute(["id_utilisateur" => $id_utilisateur, "id_recette" => $id_recette]);

    // Redirection vers la même page après la suppression des favoris
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Requête SQL pour récupérer la liste des recettes favorites de l'utilisateur connecté
$sql_favoris = "SELECT recettes.*
                FROM recettes
                JOIN Favoris ON recettes.id_recette = Favoris.id_recette
                WHERE Favoris.id_utilisateur = :id_utilisateur
                ORDER BY recettes.date_creation DESC";

$stmt_favoris = $db->prepare($sql_favoris);
$stmt_favoris->execute(["id_utilisateur" => $_SESSION["user"]["id_auteur"]]);
$recettes_favorites = $stmt_favoris->fetchAll();

// Titre de la page
$titre = "Mes recettes favorites";
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Métadonnées -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>ShareRecipe</title>

    <!-- Favicon -->
    <link rel="icon" href="icon/favicon.png" type="image/gif" />

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />
</head>

<body class="sub_page bg-gray-100 flex flex-col min-h-screen">
    <div class="hero_area">
        <!-- En-tête -->
        <header class="header_section long_section px-0">
            <nav class="navbar navbar-expand-lg custom_nav-container ">
                <a class="navbar-brand" href="index.php">
                    <span>ShareRecipe</span>
                </a>

                <!-- Barre de navigation -->
                <div class="collapse navbar-collapse mx-auto" id="navbarSupportedContent">
                    <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
                        <ul class="navbar-nav ">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
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
                                        <a class="nav-link" href="gereruser.php">Gérer les utilisateurs</a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
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
    </div>

    <!-- Section des recettes favorites -->
    <section class="blog_section layout_padding">
    <div class="container mx-auto" style="background: #f9fafa; padding-top: 50px; padding-bottom: 75px; border-radius: 5px;">
        <div class="heading_container">
            <h2>Mes Favoris</h2>
            <br>
            <?php if (!empty($recettes_favorites)): ?>
                <ul class="space-y-4 mx-auto">
                    <?php foreach ($recettes_favorites as $recette): ?>
                        <li class="bg-white p-4 shadow-md rounded-lg max-w-2xl">
                            <span class="font-bold"><?= $recette['titre'] ?></span>
                            <p>Description: <br><?= $recette['description'] ?></p>
                            <!-- Formulaire pour retirer des favoris -->
                            <form method="post" class="mt-2">
                                <div class="flex justify-between items-center">
                                    <input type="hidden" name="id_utilisateur" value="<?php echo $_SESSION['user']['id_auteur']; ?>">
                                    <input type="hidden" name="id_recette" value="<?php echo $recette['id_recette']; ?>">
                                    <button type="submit" name="remove_favoris" class="inline-flex items-center bg-transparent border-none focus:outline-none">
                                        <img src="icon/star-50.png" alt="Étoile" width="40" height="40" class="mr-2">
                                    </button>
                                    <a href="recette.php?id_recette=<?= $recette["id_recette"] ?>" style="padding: 10px 30px; background-color: #6bb7be; color: #ffffff; border-radius: 2px; border: 1px solid #6bb7be;">
                                        Voir
                                    </a>
                                </div>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-600" style="text-align: center;">Vous n'avez aucune recette en favoris pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>

