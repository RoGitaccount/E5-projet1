<?php
session_start();

// Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// Vérification du formulaire
if (!empty($_POST)) {
    // Récupération des données du formulaire ou utilisation des anciennes valeurs de la session
    $email = !empty($_POST['email']) ? strip_tags($_POST['email']) : $_SESSION["user"]["email"];
    $update_password = false;

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"][] = "L'adresse e-mail est incorrecte";
    }

    // Vérifier si le mail modifié est déjà pris par un autre utilisateur
    if (empty($_SESSION["error"]) && $email !== $_SESSION["user"]["email"]) {
        require_once "connect.php";

        $sql_email_check = "SELECT id_utilisateur FROM Utilisateurs WHERE email = :email AND id_utilisateur != :id_utilisateur";
        $query_email_check = $db->prepare($sql_email_check);
        $query_email_check->bindValue(":email", $email, PDO::PARAM_STR);
        $query_email_check->bindValue(":id_utilisateur", $_SESSION["user"]["id_auteur"], PDO::PARAM_INT);
        $query_email_check->execute();

        if ($query_email_check->rowCount() > 0) {
            $_SESSION["error"][] = "Cette adresse e-mail est déjà utilisée par un autre utilisateur.";
        }
    }

    // S'il n'y a pas d'erreurs, procéder à la mise à jour du profil
    if (empty($_SESSION["error"])) {
        require_once "connect.php";

        // Préparer la requête SQL pour mettre à jour l'utilisateur
        $sql = "UPDATE Utilisateurs SET email = :email";
        $sql_params = ["email" => $email];

        // Vérifier si un nouveau mot de passe est fourni et le valider
        if (!empty($_POST["mot_de_passe"])) {
            if (strlen($_POST["mot_de_passe"]) < 6 || !preg_match("/[0-9]/", $_POST["mot_de_passe"]) || !preg_match("/[§!@#$%^&*()\-_=+{};:,<.>]/", $_POST["mot_de_passe"])) {
                $_SESSION["error"][] = "Le mot de passe doit contenir au moins 6 caractères, un chiffre et un caractère spécial";
            } else {
                $mdphash = password_hash($_POST["mot_de_passe"], PASSWORD_DEFAULT);
                $sql .= ", mot_de_passe = :mdp";
                $sql_params["mdp"] = $mdphash;
            }
        }

        $sql .= " WHERE id_utilisateur = :id_utilisateur";
        $sql_params["id_utilisateur"] = $_SESSION["user"]["id_auteur"];

        $query = $db->prepare($sql);
        foreach ($sql_params as $param_name => $param_value) {
            $query->bindValue(":$param_name", $param_value, is_int($param_value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $result = $query->execute();

        if ($result) {
            // Mise à jour réussie, mettre à jour les données de session si nécessaire
            $_SESSION["user"]["email"] = $email;

            $_SESSION["success"] = "Profil mis à jour avec succès.";

            // Rediriger vers la page de profil ou une autre page appropriée
            header("Location: profils.php");
            exit;
        } else {
            $_SESSION["error"][] = "La mise à jour du profil a échoué";
        }
    }

    // Conserver les données soumises dans la session pour les réafficher
    $_SESSION["old_input"] = $_POST;
} else {
    // Initialiser les données avec les informations actuelles de l'utilisateur
    $_SESSION["old_input"] = [
        "email" => $_SESSION["user"]["email"]
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
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


<section class="contact_section  long_section">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <div class="heading_container">
              <h2>
                Modification du profils
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
                    <label for="email">Nouvel email :</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_SESSION["old_input"]["email"]) ? $_SESSION["old_input"]["email"] : '' ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                </div>
                <div>
                    <label for="mot_de_passe">Nouveau mot de passe :</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" />
                </div>
                <div>
                    <button type="submit">Modifier</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>




