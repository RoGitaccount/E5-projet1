<?php
session_start();


require_once "connect.php";

$sql = "SELECT recettes.*, utilisateurs.prenom 
        FROM utilisateurs 
        JOIN recettes ON utilisateurs.id_utilisateur = recettes.id_auteur
        ORDER BY recettes.date_creation DESC LIMIT 6";

$query = $db->query($sql);
$recettes = $query->fetchAll(); 
?>

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

    <div class="hero_area">
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
    </div>
    <!-- end header section -->

    <!-- blog section -->
    <section class="blog_section layout_padding">
        <div class="container" style="background:  #f9fafa;padding-top: 50px;padding-bottom: 75px; border-radius: 5px;">
            <div class="heading_container">
                <h2>
                    Les dernières recettes
                </h2>
            </div>
 <div class="row">
            <?php foreach($recettes as $recette): ?>
           
                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="box" style="height:520px; width: 340px;">
                        <div class="img-box">
                            <?php if (!empty($recette['image'])): ?>
                                 <img src="images/<?= $recette['image'] ?>" alt="img_recette" style="width: 340px; height: 310px; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <div class="detail-box">
                            <h5>
                             Titre: <br><?= strlen(strip_tags($recette["titre"])) > 20 ? substr(strip_tags($recette["titre"]), 0, 20) . '...' : strip_tags($recette["titre"]) ?>
                            </h5>
                            <p>
                            Description: <br><?= strlen(strip_tags($recette["description"])) > 30 ? substr(strip_tags($recette["description"]), 0, 30) . '...' : strip_tags($recette["description"]) ?>                            </p>
                            <a href="recette.php?id_recette=<?= $recette["id_recette"] ?>">
                                Voir
                            </a>
                        </div>
                    </div>
             </div>
             <?php endforeach; ?>
                
    </div>
            </div>
        </div>
    </div>
</section>
    <!-- end blog section -->

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
