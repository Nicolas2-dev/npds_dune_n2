<?php

namespace Npds\Routing;

use Closure;
use LogicException;
use Npds\Http\Request;
use Npds\Exceptions\Http\NotFoundHttpException;

class Router
{

    /**
     * Instance unique du routeur (singleton).
     *
     * @var Router|null
     */
    protected static ?Router $instance = null;

    /**
     * Liste des routes par méthode HTTP.
     *
     * @var array<string, array<string, array>>
     */
    protected array $routes = [
        'GET'     => [],
        'POST'    => [],
        'PUT'     => [],
        'DELETE'  => [],
        'PATCH'   => [],
        'HEAD'    => [],
        'OPTIONS' => [],
    ];

    /**
     * Patterns globaux pour les paramètres des routes.
     *
     * @var array<string, string>
     */
    protected array $patterns = [];

    /**
     * Chemin vers le dossier des assets.
     *
     * @var string
     */
    protected string $assetsPath = 'assets';

    /**
     * Chemin vers le dossier des modules.
     *
     * @var string
     */
    protected string $modulesPath = 'modules';

    /**
     * Chemin vers le dossier des thèmes.
     *
     * @var string
     */
    protected string $themesPath = 'themes';

    /**
     * Types MIME supportés pour les assets.
     *
     * @var array<string, string>
     */
    protected array $mimeTypes = [
        'js'   => 'application/javascript',
        'css'  => 'text/css',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'txt'  => 'text/plain',
    ];

    /**
     * Récupère l'instance unique du routeur.
     *
     * @return Router
     */
    public static function getInstance(): Router
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Définit le chemin vers le dossier des assets.
     *
     * @param string $path Chemin vers le dossier des assets.
     *
     * @return void
     */
    public function setAssetsPath(string $path): void
    {
        $this->assetsPath = rtrim($path, '/');
    }

    /**
     * Définit le chemin vers le dossier des modules.
     *
     * @param string $path Chemin vers le dossier des modules.
     *
     * @return void
     */
    public function setModulesPath(string $path): void
    {
        $this->modulesPath = rtrim($path, '/');
    }

    /**
     * Définit le chemin vers le dossier des thèmes.
     *
     * @param string $path Chemin vers le dossier des thèmes.
     *
     * @return void
     */
    public function setThemesPath(string $path): void
    {
        $this->themesPath = rtrim($path, '/');
    }

    /**
     * Définit une route qui répond à toutes les méthodes HTTP principales.
     *
     * @param string $route  Le chemin de la route.
     * @param mixed  $action L'action à exécuter (closure ou "Controller@method").
     *
     * @return mixed
     */
    public function any(string $route, mixed $action): mixed
    {
        $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD');

        return $this->match($methods, $route, $action);
    }

    /**
     * Définit une route pour des méthodes HTTP spécifiques.
     *
     * @param array<int, string> $methods  Méthodes HTTP autorisées.
     * @param string             $route    Chemin de la route.
     * @param mixed              $action   Action à exécuter.
     *
     * @return void
     */
    public function match(array $methods, string $route, mixed $action): void
    {
        $methods = array_map('strtoupper', $methods);

        if (in_array('GET', $methods) && ! in_array('HEAD', $methods)) {
            $methods[] = 'HEAD';
        }

        $route = '/' .trim($route, '/');

        if (! is_array($action)) {
            $action = array('uses' => $action);
        }

        foreach ($methods as $method) {
            if (array_key_exists($method, $this->routes)) {
                $this->routes[$method][$route] = $action;
            }
        }
    }

    /**
     * Traite la requête et retourne la réponse correspondant à une route.
     *
     * @param Request $request Requête HTTP à dispatcher.
     *
     * @return mixed
     *
     * @throws NotFoundHttpException Si aucune route ne correspond.
     */
    public function dispatch(Request $request): mixed
    {
        $method = $request->method();
        $path = $request->path();

        // Vérifier d'abord si c'est une requête d'asset
        if ($this->isAssetRequest($path)) {
            return $this->serveAsset($path);
        }

        // Récupère les routes par méthode HTTP.
        $routes = isset($this->routes[$method]) ? $this->routes[$method] : array();

        foreach ($routes as $route => $action) {
            $wheres = isset($action['where']) ? $action['where'] : array();

            $pattern = $this->compileRoute($route, array_merge($this->patterns, $wheres));

            if (preg_match($pattern, $path, $matches) !== 1) {
                continue;
            }

            $parameters = array_filter($matches, function ($value, $key)
            {
                return is_string($key) && ! empty($value);

            }, ARRAY_FILTER_USE_BOTH);

            $callback = isset($action['uses']) ? $action['uses'] : $this->findActionClosure($action);

            return $this->call($callback, $parameters);
        }

        throw new NotFoundHttpException('Page not found');
    }

    /**
     * Vérifie si la requête concerne un asset.
     *
     * @param string $path Chemin de la requête.
     *
     * @return bool
     */
    protected function isAssetRequest(string $path): bool
    {
        return preg_match('#/assets/#', $path) === 1;
    }

    /**
     * Sert un fichier asset.
     *
     * @param string $path Chemin de l'asset.
     *
     * @return mixed
     *
     * @throws NotFoundHttpException Si le fichier n'existe pas.
     */
    protected function serveAsset(string $path): mixed
    {
        $filePath = null;

        // Déterminer le type de requête et construire le chemin
        if (str_starts_with($path, '/assets/')) {
            // Requête classique /assets/...
            $relativePath = substr($path, 8); // strlen('/assets/') = 8
            $possiblePaths = $this->getAssetPossiblePaths($relativePath);
            
            // Chercher le fichier dans les différents emplacements
            foreach ($possiblePaths as $testPath) {
                if (file_exists($testPath) && is_file($testPath)) {
                    $filePath = $testPath;
                    break;
                }
            }
        } elseif (str_starts_with($path, '/modules/')) {
            // Requête directe /modules/nom_module/assets/...
            $filePath = $this->normalizeModuleThemePath($path, 'modules');
            
        } elseif (str_starts_with($path, '/themes/')) {
            // Requête directe /themes/nom_theme/assets/...
            $filePath = $this->normalizeModuleThemePath($path, 'themes');
        }
        
        if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
            throw new NotFoundHttpException('Asset not found');
        }
        
        // Sécurité : empêcher l'accès à des fichiers en dehors des dossiers autorisés
        $realPath = realpath($filePath);
        if (!$realPath || !$this->isPathSecure($realPath)) {
            throw new NotFoundHttpException('Asset not found');
        }

        // Déterminer le type MIME
        $extension = pathinfo($realPath, PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes[$extension] ?? 'application/octet-stream';

        // Définir les en-têtes HTTP
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realPath));
        
        // Ajouter des en-têtes de cache pour améliorer les performances
        $lastModified = filemtime($realPath);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header('Cache-Control: public, max-age=3600'); // Cache 1 heure
        
        // Vérifier si le client a déjà le fichier en cache
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($ifModifiedSince >= $lastModified) {
                http_response_code(304); // Not Modified
                return '';
            }
        }

        // Lire et envoyer le contenu du fichier
        readfile($realPath);
        return '';
    }

    /**
     * Normalise le chemin d'un module ou thème en gérant la casse.
     *
     * @param string $path Chemin de la requête (ex: /modules/geoloc/assets/...)
     * @param string $type Type: 'modules' ou 'themes'
     *
     * @return string|null Chemin normalisé ou null si non trouvé
     */
    protected function normalizeModuleThemePath(string $path, string $type): ?string
    {
        // Vérifier que le chemin contient bien "/assets/"
        if (strpos($path, '/assets/') === false) {
            return null;
        }

        // Extraire les parties du chemin
        // Ex: /modules/geoloc/assets/css/style.css -> ['', 'modules', 'geoloc', 'assets', 'css', 'style.css']
        $pathParts = explode('/', $path);
        
        if (count($pathParts) < 4) { // Au minimum ['', 'modules', 'nom', 'assets']
            return null;
        }

        $moduleOrThemeName = $pathParts[2]; // 'geoloc'
        $basePath = $type === 'modules' ? $this->modulesPath : $this->themesPath;
        
        // Essayer d'abord le nom tel quel
        $directPath = ltrim($path, '/');
        if (file_exists($directPath)) {
            return $directPath;
        }
        
        // Essayer avec la première lettre en majuscule
        $normalizedName = ucfirst(strtolower($moduleOrThemeName));
        $pathParts[2] = $normalizedName;
        $normalizedPath = implode('/', array_slice($pathParts, 1)); // Enlever le premier élément vide
        
        if (file_exists($normalizedPath)) {
            return $normalizedPath;
        }
        
        // Essayer de trouver le dossier réel (insensible à la casse)
        if (is_dir($basePath)) {
            $realDirs = array_filter(glob($basePath . '/*'), 'is_dir');
            foreach ($realDirs as $realDir) {
                $realDirName = basename($realDir);
                if (strtolower($realDirName) === strtolower($moduleOrThemeName)) {
                    // Remplacer le nom par le nom réel
                    $pathParts[2] = $realDirName;
                    $foundPath = implode('/', array_slice($pathParts, 1));
                    if (file_exists($foundPath)) {
                        return $foundPath;
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Génère la liste des chemins possibles pour un asset.
     *
     * @param string $relativePath Chemin relatif de l'asset.
     *
     * @return array<string> Liste des chemins à tester.
     */
    protected function getAssetPossiblePaths(string $relativePath): array
    {
        $paths = [];
        
        // 1. Dossier assets principal
        $paths[] = $this->assetsPath . '/' . ltrim($relativePath, '/');
        
        // 2. Dossiers modules (cherche dans tous les modules)
        if (is_dir($this->modulesPath)) {
            $modules = array_filter(glob($this->modulesPath . '/*'), 'is_dir');
            foreach ($modules as $moduleDir) {
                $assetPath = $moduleDir . '/assets/' . ltrim($relativePath, '/');
                $paths[] = $assetPath;
            }
        }
        
        // 3. Dossiers themes (cherche dans tous les thèmes)
        if (is_dir($this->themesPath)) {
            $themes = array_filter(glob($this->themesPath . '/*'), 'is_dir');
            foreach ($themes as $themeDir) {
                $assetPath = $themeDir . '/assets/' . ltrim($relativePath, '/');
                $paths[] = $assetPath;
            }
        }
        
        return $paths;
    }

    /**
     * Vérifie si le chemin est sécurisé (dans les dossiers autorisés).
     *
     * @param string $realPath Chemin réel du fichier.
     *
     * @return bool
     */
    protected function isPathSecure(string $realPath): bool
    {
        // Vérifier le dossier assets principal
        $assetsRealPath = realpath($this->assetsPath);
        if ($assetsRealPath && str_starts_with($realPath, $assetsRealPath)) {
            return true;
        }
        
        // Vérifier les dossiers modules
        if (is_dir($this->modulesPath)) {
            $modulesRealPath = realpath($this->modulesPath);
            if ($modulesRealPath && str_starts_with($realPath, $modulesRealPath)) {
                // S'assurer que le chemin contient '/assets/'
                return strpos($realPath, '/assets/') !== false;
            }
        }
        
        // Vérifier les dossiers themes
        if (is_dir($this->themesPath)) {
            $themesRealPath = realpath($this->themesPath);
            if ($themesRealPath && str_starts_with($realPath, $themesRealPath)) {
                // S'assurer que le chemin contient '/assets/'
                return strpos($realPath, '/assets/') !== false;
            }
        }
        
        return false;
    }

    /**
     * Compile une route en expression régulière.
     *
     * @param string            $route    Route à compiler.
     * @param array<string, string> $patterns Patterns personnalisés pour les paramètres.
     *
     * @return string Expression régulière prête pour preg_match.
     */
    protected function compileRoute(string $route, array $patterns): string
    {
        $optionals = 0;

        $variables = array();

        $regexp = preg_replace_callback('#/\{(.*?)(\?)?\}#', function ($matches) use ($route, $patterns, &$optionals, &$variables)
        {
            //@list(, $name, $optional) = $matches;

            $name     = $matches[1];
            $optional = $matches[2] ?? null; 

            if (in_array($name, $variables)) {
                throw new LogicException("Pattern [$route] cannot reference variable name [$name] more than once.");
            }

            $variables[] = $name;

            $pattern = isset($patterns[$name]) ? $patterns[$name] : '[^/]+';

            if ($optional) {
                $optionals++;

                return sprintf('(?:/(?P<%s>%s)', $name, $pattern);
            } else if ($optionals > 0) {
                throw new LogicException("Pattern [$route] cannot reference variable [$name] after one or more optionals.");
            }

            return sprintf('/(?P<%s>%s)', $name, $pattern);

        }, $route);

        if ($optionals > 0) {
            $regexp .= str_repeat(')?', $optionals);
        }

        return '#^' .$regexp .'$#s';
    }

    /**
     * Recherche une closure dans la définition d'une route.
     *
     * @param array<string, mixed> $action Action définie pour la route.
     *
     * @return Closure|null
     */
    protected function findActionClosure(array $action): ?Closure
    {
        foreach ($action as $key => $value) {
            if (is_numeric($key) && ($value instanceof Closure)) {
                return $value;
            }
        }

        // Aucun Closure trouvé
        return null;
    }

    /**
     * Appelle l'action correspondante à une route.
     *
     * @param mixed        $callback   Closure ou "Controller@method".
     * @param array<string, mixed> $parameters Paramètres à passer.
     *
     * @return mixed
     */
    protected function call(mixed $callback, array $parameters): mixed
    {
        if ($callback instanceof Closure) {
            return call_user_func_array($callback, $parameters);
        }

        list ($controller, $method) = explode('@', $callback);

        if (! class_exists($controller)) {
            throw new LogicException("Controller [$controller] not found.");
        }

        // Crée l'instance du contrôleur et vérifie la méthode spécifiée.
        else if (! method_exists($instance = new $controller(), $method)) {
            throw new LogicException("Controller [$controller] has no method [$method].");
        }

        return $instance->callAction($method, $parameters);
    }

    /**
     * Définit un pattern pour un paramètre de route.
     *
     * @param string $key     Nom du paramètre.
     * @param string $pattern Expression régulière.
     *
     * @return void
     */
    public function pattern(string $key, string $pattern): void
    {
        $this->patterns[$key] = $pattern;
    }

    /**
     * Appel dynamique pour ajouter des routes via méthodes HTTP (get, post, put...).
     *
     * @param string $method     Méthode appelée.
     * @param array  $parameters Paramètres de la méthode.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (array_key_exists($key = strtoupper($method), $this->routes)) {
            array_unshift($parameters, array($key));

            $method = 'match';
        }

        return call_user_func_array(array($this, $method), $parameters);
    }

}
