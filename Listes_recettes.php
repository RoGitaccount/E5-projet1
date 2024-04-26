<?php
session_start();

require_once "connect.php";



// Fonction pour vérifier si la recette est dans les favoris de l'utilisateur
function recetteDansFavoris($id_recette, $id_utilisateur) {
    global $db;
    $sql = "SELECT COUNT(*) FROM Favoris WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";
    $stmt = $db->prepare($sql);
    $stmt->execute(["id_utilisateur" => $id_utilisateur, "id_recette" => $id_recette]);
    return (bool)$stmt->fetchColumn();
}

// Traitement de la soumission du formulaire pour ajouter ou retirer des favoris
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_favoris"])) {
        // Ajouter aux favoris
        $id_utilisateur = $_SESSION["user"]["id_auteur"];
        $id_recette = strip_tags($_POST["id_recette"]);
        $sql = "INSERT INTO Favoris (id_utilisateur, id_recette) VALUES (:id_utilisateur, :id_recette)";
    } elseif (isset($_POST["remove_favoris"])) {
        // Retirer des favoris
        $id_utilisateur = $_SESSION["user"]["id_auteur"];
        $id_recette = strip_tags($_POST["id_recette"]);
        $sql = "DELETE FROM Favoris WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";
    }

    // Exécution de la requête SQL
    $stmt = $db->prepare($sql);
    $stmt->execute(["id_utilisateur" => $id_utilisateur,
                     "id_recette" => $id_recette]);

    // Redirection vers la même page après l'ajout ou la suppression des favoris
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Récupération des recettes
$sql = "SELECT recettes.*, utilisateurs.prenom 
        FROM utilisateurs 
        JOIN recettes ON utilisateurs.id_utilisateur = recettes.id_auteur
        ORDER BY recettes.date_creation DESC";

$query = $db->query($sql);
$recettes = $query->fetchAll(); 

$titre = "Liste des recettes"; 
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

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-4"><?= $titre ?></h1>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach($recettes as $recette): ?>
        <article class="border rounded p-4 bg-white">
            <h1 class="text-xl font-semibold mb-2">
                <a href="recette.php?id_recette=<?= $recette["id_recette"] ?>">
                    <p>Titre: <br><?= strlen(strip_tags($recette["titre"])) > 22 ? substr(strip_tags($recette["titre"]), 0, 22) . '...' : strip_tags($recette["titre"]) ?></p>
                </a>
                <?php if (!empty($recette['image'])): ?>
                    <img src="images/<?= $recette['image'] ?>" alt="img_recette" style="width: 340px; height: 310px; object-fit: cover;">
                <?php endif; ?>
            </h1>
            <p class="text-gray-600">Publié par: <?= strlen(strip_tags($recette["prenom"])) > 20 ? substr(strip_tags($recette["prenom"]), 0, 20) . '...' : strip_tags($recette["prenom"]) ?></p>
            <p class="text-gray-600">Le <?= $recette["date_creation"] ?></p>
            <div class="mt-2">Description: <br><?= strlen(strip_tags($recette["description"])) > 20 ? substr(strip_tags($recette["description"]), 0, 20) . '...' : strip_tags($recette["description"]) ?></p>
            

            <?php if (isset($_SESSION["user"]) && $recette["id_auteur"] !== $_SESSION["user"]["id_auteur"]): ?>    <!-- Formulaire pour ajouter ou retirer des favoris -->
    <form method="post">
        <input type="hidden" name="id_utilisateur" value="<?php echo $_SESSION['user']['id_auteur']; ?>">
        <input type="hidden" name="id_recette" value="<?php echo $recette['id_recette']; ?>">
        <?php if (recetteDansFavoris($recette['id_recette'], $_SESSION['user']['id_auteur'])): ?>
            <!-- Si la recette est dans les favoris, afficher le bouton pour retirer des favoris -->
            Retirer des favoris : <br>
            <button type="submit" name="remove_favoris">
            <img src="icon/star-50.png" alt="Étoile" width="40" height="40" /> 
            </button>
        <?php else: ?>
            <!-- Sinon, afficher le bouton pour ajouter aux favoris -->
            Ajouter au favoris : <br>
             <button type="submit" name="submit_favoris">
                <img src="icon/starout-50.png" alt="Étoile" width="40" height="40" />
            </button>

        <?php endif; ?>
    </form>
<?php endif; ?>

        </article>
    <?php endforeach; ?>
    </section>
</div>

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
