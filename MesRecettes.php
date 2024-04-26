<?php
session_start();


if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

// J'ai un ID, donc je le récupère
$id_auteur = $_SESSION["user"]["id_auteur"];

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
    <title>Mes recettes</title>

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

        <br>

        <div class="flex justify-between items-center p-4 bg-white">
            <!-- Bouton "Ajouter" -->
            <a href="ajouter.php" class="btn-add inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                <img src="icon/add.png" alt="ajouter">
                Ajouter
            </a>

            

            <!-- Formulaire de recherche -->
            <form action='' method="get" class="flex gap-2">
                <div class="my-4">
                    <label for="search" class="block mb-1">Rechercher dans mes recettes :</label>
                    <input type="text" id="search" name="rcpsearch" class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring focus:border-blue-500" value="<?php echo isset($_GET['rcpsearch']) ? htmlspecialchars($_GET['rcpsearch']) : '' ?>">
                
                    <input type="submit" name="submit" value="Chercher" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 cursor-pointer h-auto w-auto">
                </div> 
            </form>

            <a href="MesRecettes.php">
                <img src="icon/refresh.png"> <p>rénitialiser la recherche</p>
            </a>
        </div>
<br>
        <a  href='Listes_recettes.php'>Retour vers les recettes</a>

        <?php
        
        // Connexion à la base de données
        require_once "connect.php";

        // Si le formulaire de recherche est soumis
        if (isset($_GET['submit'])) {
            // Nettoyage de l'entrée de recherche
            $trouver = strip_tags($_GET['rcpsearch'] ?? '');
            $recherche = "%$trouver%";

            // Préparation de la requête SQL avec une clause WHERE pour la recherche
            $sql = "SELECT * FROM recettes WHERE id_auteur = :id_auteur AND Titre LIKE :recherche";

            // Préparation de la requête
            $query = $db->prepare($sql);

            // Injection des paramètres
            $query->bindValue(":id_auteur", $id_auteur, PDO::PARAM_INT);
            $query->bindValue(":recherche", $recherche, PDO::PARAM_STR);

            // Exécution de la requête
            $query->execute();

            // Récupération des résultats
            $recettes = $query->fetchAll(PDO::FETCH_ASSOC);

            // Vérification si des résultats ont été trouvés
            if (empty($recettes)) {
                echo "<p class='empty-message text-gray-600 text-xl'>Aucune recette correspond à votre recherche.</p>";
            } else {
                // Affichage des recettes correspondantes
                echo "<table class='w-full bg-white shadow-md rounded-lg overflow-hidden my-4'>
                        <thead class='bg-gray-200 uppercase text-sm text-gray-600'>
                            <tr>
                                <th class='py-3 px-4 font-semibold'>Titre</th>
                                <th class='py-3 px-4 font-semibold'>Description</th>
                                <th class='py-3 px-4 font-semibold'>Ingrédients</th>
                                <th class='py-3 px-4 font-semibold'>Étapes préparation</th>
                                <th class='py-3 px-4 font-semibold'>Temps préparation</th>
                                <th class='py-3 px-4 font-semibold'>Temps cuisson</th>
                                <th class='py-3 px-4 font-semibold'>Voir la recette</th>
                                <th class='py-3 px-4 font-semibold'>Modifier</th>
                                <th class='py-3 px-4 font-semibold'>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody class='text-gray-600 text-sm divide-y divide-gray-200'>";

                foreach ($recettes as $recette) {
                    echo "<tr>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['titre']) > 50 ? substr($recette['titre'], 0, 50) . '...' : $recette['titre']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['description']) > 50 ? substr($recette['description'], 0, 50) . '...' : $recette['description']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['ingredients']) > 50 ? substr($recette['ingredients'], 0, 50) . '...' : $recette['ingredients']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['etapes_preparation']) > 50 ? substr($recette['etapes_preparation'], 0, 50) . '...' : $recette['etapes_preparation']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . $recette['temps_preparation'] . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . $recette['temps_cuisson'] . "</td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='recette.php?id_recette=" . $recette['id_recette'] . "' class='text-blue-500 hover:underline'>Recette</a></td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='modifier.php?id_recette=" . $recette['id_recette'] . "'><img src='icon/edit.png' alt='Modifier' class='w-5 h-5'></a></td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='supprimer.php?id_recette=" . $recette['id_recette'] . "'><img src='icon/delete.png' alt='Supprimer' class='w-5 h-5'></a></td>
                        </tr>";
                }

                echo "</tbody>
                    </table>";
            }
        } else {
            // Si le formulaire de recherche n'est pas soumis, afficher toutes les recettes de l'utilisateur
            $sql = "SELECT * FROM `recettes` WHERE `id_auteur` = :id_auteur";

            // Préparation de la requête
            $query = $db->prepare($sql);

            // Injection des paramètres
            $query->bindValue(":id_auteur", $id_auteur, PDO::PARAM_INT);

            // Exécution de la requête
            $query->execute();

            // Récupération des résultats
            $recettes = $query->fetchAll(PDO::FETCH_ASSOC);

            // Vérification si des recettes ont été trouvées
            if (empty($recettes)) {
                echo "<p class='empty-message text-gray-600 text-xl'>Vous n'avez pas encore de recette.</p>";
            } else {
                // Affichage des recettes
                echo "<table class='w-full bg-white shadow-md rounded-lg overflow-hidden my-4'>
                        <thead class='bg-gray-200 uppercase text-sm text-gray-600'>
                            <tr>
                                <th class='py-3 px-4 font-semibold'>Titre</th>
                                <th class='py-3 px-4 font-semibold'>Description</th>
                                <th class='py-3 px-4 font-semibold'>Ingrédients</th>
                                <th class='py-3 px-4 font-semibold'>Étapes préparation</th>
                                <th class='py-3 px-4 font-semibold'>Temps préparation</th>
                                <th class='py-3 px-4 font-semibold'>Temps cuisson</th>
                                <th class='py-3 px-4 font-semibold'>Voir la recette</th>
                                <th class='py-3 px-4 font-semibold'>Modifier</th>
                                <th class='py-3 px-4 font-semibold'>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody class='text-gray-600 text-sm divide-y divide-gray-200'>";

                foreach ($recettes as $recette) {
                    echo "<tr>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['titre']) > 20 ? substr($recette['titre'], 0, 20) . '...' : $recette['titre']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['description']) > 18 ? substr($recette['description'], 0, 18) . '...' : $recette['description']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['ingredients']) > 18 ? substr($recette['ingredients'], 0, 18) . '...' : $recette['ingredients']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . (strlen($recette['etapes_preparation']) > 18 ? substr($recette['etapes_preparation'], 0, 18) . '...' : $recette['etapes_preparation']) . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . $recette['temps_preparation'] . "</td>
                            <td class='py-3 px-4 sm:w-1/6'>" . $recette['temps_cuisson'] . "</td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='recette.php?id_recette=" . $recette['id_recette'] . "' class='text-blue-500 hover:underline'>Recette</a></td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='modifier.php?id_recette=" . $recette['id_recette'] . "'><img src='icon/edit.png' alt='Modifier' class='w-5 h-5'></a></td>
                            <td class='py-3 px-4 sm:w-1/6'><a href='supprimer.php?id_recette=" . $recette['id_recette'] . "'><img src='icon/delete.png' alt='Supprimer' class='w-5 h-5'></a></td>
                        </tr>";
                }

                echo "</tbody>
                    </table>";
            }
        }

        ?>

    </div>
</body>

</html>

