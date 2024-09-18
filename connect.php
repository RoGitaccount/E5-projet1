<?php
// Charger l'autoloader généré par Composer
require_once __DIR__ . '/vendor/autoload.php';

// Charger les variables d'environnement depuis le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Utiliser les variables d'environnement pour la connexion à la base de données
$dsn = "mysql:dbname=" . $_ENV['DB_NAME'] . ";host=" . $_ENV['DB_HOST'];

try {
    $db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $db->exec("set names 'utf8'");
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die($e->getMessage());
}
