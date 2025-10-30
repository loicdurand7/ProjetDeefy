<?php
declare(strict_types=1);


require_once 'vendor/autoload.php';
require_once 'src/classes/utils/html.php';
require_once 'src/classes/utils/auth.php';
session_start();

use iutnc\deefy\repository\DeefyRepository;
DeefyRepository::setConfig(__DIR__ . '/config.ini'); // Charger la config avant l'instance

if (!isset($_SESSION['playlists'])) {
    $_SESSION['playlists'] = []; // 
}

use iutnc\deefy\dispatch\Dispatcher;


$a = isset($_GET['action']) ? $_GET['action'] : '';

$app = new Dispatcher($a);
$app->run();