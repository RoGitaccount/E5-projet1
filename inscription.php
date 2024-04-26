<!-- refaire la navbar -->

<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Vérification du formulaire
if (!empty($_POST)) {
    // Vérification des champs requis
    $required_fields = ["nom", "prenom", "email", "mot_de_passe"];
    $form_complete = true;
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $form_complete = false;
            $_SESSION["error"][] = "Le champ $field est requis";
        }
    }

    if ($form_complete) {
        // Récupération des données
        $nom = strip_tags($_POST['nom']);
        $prenom = strip_tags($_POST['prenom']);
        $email = strip_tags($_POST['email']);
        $_SESSION["error"] = [];

        // Validation de l'email
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "L'addresse e-mail est incorrecte";
        }

// Validation du mot de passe
if (strlen($_POST["mot_de_passe"]) < 6 || !preg_match("/[0-9]/", $_POST["mot_de_passe"]) || !preg_match("/[§!@#$%^&*()\-_=+{};:,<.>]/", $_POST["mot_de_passe"])) {
    $_SESSION["error"][] = "Le mot de passe doit contenir plus de 6 caractères, un chiffre et un caractère spécial";
}
if (empty($_SESSION["error"])) {
  // Définition du rôle par défaut
  $role = "utilisateur";

  // Hashage du mot de passe
  $mdphash = password_hash($_POST["mot_de_passe"], PASSWORD_DEFAULT);
          
  require_once "connect.php";

  // Vérification si l'email existe déjà dans la base de données
  $sql = "SELECT * FROM utilisateurs WHERE email = :email";
  $query = $db->prepare($sql);
  $query->bindValue(":email", $email, PDO::PARAM_STR);
  $query->execute();

  if ($query->rowCount() > 0) {
      $_SESSION["error"][] = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
  } else {
      // Insertion dans la base de données
      $sql = "INSERT INTO Utilisateurs (nom,prenom,email,mot_de_passe,role) VALUES (:nom,:prenom,:email,:mdp,:role)";
      $query = $db->prepare($sql);
      $query->bindValue(":nom", $nom, PDO::PARAM_STR);
      $query->bindValue(":prenom", $prenom, PDO::PARAM_STR);
      $query->bindValue(":email", $email, PDO::PARAM_STR);
      $query->bindValue(":mdp", $mdphash, PDO::PARAM_STR);
      $query->bindValue(":role", $role, PDO::PARAM_STR); // Ajout du rôle
      $result = $query->execute();

      if ($result) {
          // Récupérer l'ID de l'utilisateur nouvellement inscrit
          $id_auteur = $db->lastInsertId();

          // Inscription réussie, créer la session
          $_SESSION["user"] = [
              "nom" => $nom,
              "prenom" => $prenom,
              "email" => $email,
              "id_auteur" => $id_auteur,  // Ajouter l'id_auteur à la session
              "role" => $role
          ];

          // Rediriger vers la page de profil
          header("Location: index.php");
          exit;
      } else {
          $_SESSION["error"][] = "L'ajout du profil n'a pas pu aboutir";
      }
  }
}
    } else {
        $_SESSION["error"] = ["Le formulaire est incomplet"];
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

    <title>Inscription</title>

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

    <!-- end header section -->
  </div>
<!-- ______________________________________________ -->
  <!-- contact section -->
  <section class="contact_section  long_section">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <div class="heading_container">
              <h2>
                Inscription
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
                <input type="text" name="nom" placeholder="Veuillez saisir votre Nom" required/>
              </div>
              <div>
                <input type="text" name="prenom" placeholder="Veuillez saisir votre Prenom" required />
              </div>
              <div>
                <input type="email" name="email" placeholder="Veuillez saisir votre Email" required />
              </div>
              <div>
                <input type="password" name="mot_de_passe" placeholder="Veuillez saisir un Mot de passe" required/>
              </div>
              <div class="btn_box">
                <button>
                  M'inscrire
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