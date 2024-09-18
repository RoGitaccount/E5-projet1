<?php
//on démarre la session PHP
session_start();

// Initialisation de $id_auteur à une valeur par défaut (dans ce cas, null)
$id_auteur = null;

// Vérifier si la clé "user" existe dans la session et n'est pas vide
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["id_auteur"])) {
    // Si la clé "user" existe et contient la clé "id_auteur", récupérer sa valeur
    $id_auteur = strip_tags($_SESSION["user"]["id_auteur"]);
}

// On vérifie si on a un ID CAD on vérifie si $_GET["id_recette"] existe et n'est pas vide.
if (!isset($_GET["id_recette"]) || empty($_GET["id_recette"])) {
    // Je n'ai pas d'ID     Si $_GET["id"] n'existe pas ou est vide, on effectue une redirection vers "Listes_recettes.php".
    header("Location: Listes_recettes.php");
    exit;
}

// J'ai un ID, donc je le récupère
$id = $_GET["id_recette"];

//on se co à la BDD
require_once "connect.php";

//on va chercher la recette dans la base
//on écrit la requete
$sql = "SELECT * FROM `recettes` WHERE `id_recette` =:id_recette";

//on prepare la requete
$query = $db->prepare($sql);
//on injecte les parametre
$query->bindValue(":id_recette", $id, PDO::PARAM_INT);
//on execute
$query->execute();
//on recup la recette
$recette = $query->fetch();

//on vérifie si l'article est vide 
if (!$recette) {
    //pas d'article erreur 404
    http_response_code(404);
    echo "Recette inexistante";
    exit;
}

//avant d'inclure le header, je dois définir le titre
$titre = strip_tags($recette["titre"]);

//header HTML et PHP combinés
?>
<!DOCTYPE html>
<html lang="en">

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
  <title><?= $titre ?? "Page sans nom" ?></title>




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
    <!-- end header section -->

<body class="bg-gray-100">

<article class="max-w-xl mx-auto mt-8">
        <h1 class="text-4xl font-bold mb-4"><?= strlen(strip_tags($recette["titre"])) > 20 ? wordwrap(strip_tags($recette["titre"]), 20, "<br>",true) : strip_tags($recette["titre"]) ?></p>
</h1>
                <?php if (!empty($recette['image'])): ?>
                    <img src="images/<?= $recette['image'] ?>" alt="img_recette" style="width: 340px; height: 310px; object-fit: cover;">
                <?php endif; ?>
        <p class="mb-2">Publié le <?= $recette["date_creation"] ?></p>
        <div class="mb-4">
            <h2 class="text-lg font-bold mb-2">Description:</h2>
            <p class="mb-2"><?= strlen(strip_tags($recette["description"])) > 50 ? wordwrap(strip_tags($recette["description"]), 50, "<br>", true) : strip_tags($recette["description"]); ?></p>
            <h2 class="text-lg font-bold mb-2">Ingrédients:</h2>
            <p class="mb-2"><?= strlen(strip_tags($recette["ingredients"])) > 50 ? wordwrap(strip_tags($recette["ingredients"]), 50, "<br>", true) : strip_tags($recette["ingredients"]); ?></p>
            <h2 class="text-lg font-bold mb-2">Étapes de préparation:</h2>
            <p class="mb-2"><?= strlen(strip_tags($recette["etapes_preparation"])) > 50 ? wordwrap(strip_tags($recette["etapes_preparation"]), 50, "<br>", true) : strip_tags($recette["etapes_preparation"]); ?></p>
            <h2 class="text-lg font-bold mb-2">Temps de préparation:</h2>
            <p class="mb-2"><?= strip_tags($recette["temps_preparation"]) ?></p>
            <h2 class="text-lg font-bold mb-2">Temps de cuisson:</h2>
            <p class="mb-2"><?= strip_tags($recette["temps_cuisson"]) ?></p>
        </div>

        <?php if ($id_auteur && $id_auteur == strip_tags($recette["id_auteur"])): ?>
            <p><a href="MesRecettes.php" class="text-blue-500 hover:underline">Retour vers mes recettes</a></p>
        <?php else: ?>
            <p><a href="Listes_recettes.php" class="text-blue-500 hover:underline">Retour vers la liste des recettes</a></p>
        <?php endif; ?>

</article>

<br><br>

 <!-- footer section -->
 <footer class="footer_section">
    <div class="container">
      <p>
        &copy; <span id="displayYear"></span> ShareRecipe
      </p>
    </div>
    <!-- Votre script JavaScript -->
<script>
    // Fonction pour afficher ou masquer le footer en fonction de la position de défilement
    function toggleFooterVisibility() {
        var footer = document.querySelector('.footer_section');
        var scrollPosition = window.scrollY;
        var windowHeight = window.innerHeight;
        var bodyHeight = document.body.offsetHeight;

        if ((scrollPosition + windowHeight) >= bodyHeight) {
            // Si la position de défilement + la hauteur de la fenêtre est égale ou supérieure à la hauteur du corps,
            // alors nous sommes en bas de la page, donc affichons le footer
            footer.style.display = 'block';
        } else {
            // Sinon, masquons le footer
            footer.style.display = 'none';
        }
    }

    // Écouteur d'événements pour détecter le défilement de la fenêtre
    window.addEventListener('scroll', toggleFooterVisibility);
</script>
  </footer>
  <!-- footer section -->


  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
  <!-- Google Map -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap"></script>
  <!-- End Google Map -->

</body>

</html>

