<?php

// Ce fichier nous permet d'émuler la fonctionnalité "mod_rewrite" d'Apache avec le serveur web intégré de PHP.
// Il fournit un moyen pratique de tester l'application sans avoir installé un véritable logiciel de serveur web.

// Usage:
// php -S localhost:8080 -t public/ server.php

$publicPath = 'public/';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = urldecode($uri);

$requested = $publicPath .$uri;

if (($uri !== '/') && file_exists($requested)) {
    return false;
}

require_once $publicPath .'index.php';
