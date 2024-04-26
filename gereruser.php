<?php
session_start();



if ($_SESSION["user"]["role"] != "admin") {
    header("Location: index.php");
    exit;
}


require_once "connect.php";

// Récupération des utilisateurs
$sql = "SELECT * FROM utilisateurs WHERE role != 'admin'";
$query = $db->query($sql);
$utilisateurs = $query->fetchAll(); 

$titre = "Liste des utilisateurs"; 
?>

<!DOCTYPE html>
<html>



<!DOCTYPE html>
<html>

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <link rel="icon" href="icon/favicon.png" type="image/gif" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>ShareRecipe</title>

    <!-- Inclure le fichier CSS de Tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />

    <!-- font awesome style -->
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />

</head>

<body class="sub_page bg-gray-100 flex flex-col min-h-screen">

    <!-- header section strats -->
    <header class="header_section long_section px-0">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
            <a class="navbar-brand" href="index.php">
                <span>
                    ShareRecipe
                </span>
            </a>


            <!-- navbar -->
            <div class="collapse navbar-collapse mx-auto" id="navbarSupportedContent">
                <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
                    <ul class="navbar-nav ">
                        <li class="nav-item ">
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
                                <span>
                                    Inscription
                                </span>
                            </a>
                        </div>

                        <div class="quote_btn-container">
                            <a href="connexion.php">
                                <span>
                                    Connexion
                                </span>
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php else: ?>
                      <div class="quote_btn-container">
                                
                                <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                                

                                <a href="profils.php">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    <span>
                                        <?=strip_tags($_SESSION["user"]["prenom"]) ?>
                                    </span>
                                </a>
                      </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4"><?= $titre ?></h1>

        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-8">
            <?php foreach($utilisateurs as $utilisateur): ?>
                <article class="bg-white shadow-md p-4 rounded-lg">
                    <p class="text-lg font-semibold">Nom: <?= strlen(strip_tags($utilisateur["nom"])) > 20 ? substr(strip_tags($utilisateur["nom"]), 0, 20) . '...' : strip_tags($utilisateur["nom"]) ?></p>
                    <p class="text-lg font-semibold">Prénom: <?= strlen(strip_tags($utilisateur["prenom"])) > 20 ? substr(strip_tags($utilisateur["prenom"]), 0, 20) . '...' : strip_tags($utilisateur["prenom"]) ?></p>
                    <p class="text-lg font-semibold">Email: <?= strlen(strip_tags($utilisateur["email"])) > 20 ? substr(strip_tags($utilisateur["email"]), 0, 20) . '...' : strip_tags($utilisateur["email"]) ?></p>
                  
                    <a href="supuser.php?id_utilisateur=<?= $utilisateur['id_utilisateur'] ?>" class="text-red-500 hover:text-red-700">
                        <img src="icon/delete.png" alt="Supprimer" class="w-5 h-5">
                    </a>
                </article>
            <?php endforeach; ?>
        </section>
    </div>

</body>

</html>

