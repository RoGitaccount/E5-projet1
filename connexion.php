<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: MesRecettes.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification des champs requis
    $required_fields = ["email", "mot_de_passe"];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $_SESSION["error"][] = "Le champ $field est requis";
        }
    }

    // Si le formulaire est complet, on procède à la vérification des informations
    if (empty($_SESSION["error"])) {
        // Accéder aux valeurs de $_POST
        $email = strip_tags($_POST["email"]);
        $mot_de_passe = $_POST["mot_de_passe"];

        require_once "connect.php";

        // Requête SQL préparée pour récupérer les informations de l'utilisateur à partir de son email
        $sql = "SELECT * FROM utilisateurs WHERE email=:email";
        $query = $db->prepare($sql);
        $query->bindValue(":email", $email, PDO::PARAM_STR);
        $query->execute();

        // Vérification si l'utilisateur existe dans la base de données
        if ($query->rowCount() > 0) {
            // Utilisateur trouvé, vérification du mot de passe
            $row = $query->fetch();

            if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
                // Mot de passe correct, utilisateur connecté
                // Création de la session utilisateur
                $_SESSION["user"] = [
                    "nom" => $row["nom"],
                    "prenom" => $row["prenom"],
                    "email" => $row["email"],
                    "id_auteur" => $row["id_utilisateur"],
                    "role" => $row["Role"]
                ];

                // Redirection vers la page index avec le nom du profils
                header("Location: index.php");
                exit;
            } else {
                // Mot de passe incorrect
                $_SESSION["error"][] = "Les informations renseignées sont incorrectes";
            }
        } else {
            // Aucun utilisateur trouvé avec cet email
            $_SESSION["error"][] = "Les informations renseignées sont incorrectes";
        }

        // Fermeture de la connexion
        $db = null;
    }
}
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

    <title>Connexion</title>

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

<body class="sub_page">
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
                            <?php endif; ?>

                            <?php if($_SESSION["user"]["role"] == "admin"): ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="contact.html">Gérer les utilisateurs</a>
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
                            <li class="nav-item">
                                <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                            </li>

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
 
    <!-- ______________________________________________ -->
    <!-- contact section -->
    <section class="contact_section  long_section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="form_container">
                        <div class="heading_container">
                            <h2>
                                Connexion
                            </h2>
                        </div>

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

                        <form action="" method="post">
                            <div>
                                <input type="email" name="email" placeholder="Email" required />
                            </div>
                            <div>
                                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required />
                            </div>
                            <div class="btn_box">
                                <button>
                                    Se connecter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end contact section -->

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
