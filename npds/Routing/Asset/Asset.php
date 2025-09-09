<?php

namespace Npds\Routing\Asset;

use Npds\Http\Request;
use Npds\Exceptions\Http\NotFoundHttpException;

/**
 * // Configuration optionnelle des chemins d'assets (si différents des valeurs par défaut)
 * $assetHandler = Asset::getInstance();
 * //$assetHandler->setAssetsPath('assets');
 * //$assetHandler->setModulesPath('modules');
 * //$assetHandler->setThemesPath('themes');
 * 
 * // Tenter de servir un asset en premier
 * $assetHandler->dispatch($request);
 */

class Asset
{
    /**
     * Instance unique de la classe Asset (singleton).
     *
     * @var Asset|null
     */
    protected static ?Asset $instance = null;

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
     * Récupère l'instance unique de la classe Asset.
     *
     * @return Asset
     */
    public static function getInstance(): Asset
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Point d'entrée pour dispatcher les assets avec cette instance.
     *
     * @param Request $request Requête HTTP
     *
     * @return bool True si un asset a été servi, false sinon
     */
    public function dispatch(Request $request): bool
    {
        $path = $request->path();

        if ($this->isAssetRequest($path)) {
            $this->serveAsset($path);
            return true;
        }

        return false;
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
     * Vérifie si la requête concerne un asset.
     *
     * @param string $path Chemin de la requête.
     *
     * @return bool
     */
    protected function isAssetRequest(string $path): bool
    {
        return preg_match('#/assets/#', $path) === 1 ||
               str_starts_with($path, '/modules/') ||
               str_starts_with($path, '/themes/');
    }

    /**
     * Sert un fichier asset.
     *
     * @param string $path Chemin de l'asset.
     *
     * @return void
     *
     * @throws NotFoundHttpException Si le fichier n'existe pas.
     */
    protected function serveAsset(string $path): void
    {
        $filePath = null;
        
        // Vérifier si le chemin correspond au dossier assets configuré
        $assetsBasePath = '/' . trim($this->assetsPath, '/');
        if (str_starts_with($path, $assetsBasePath)) {
            // Requête directe vers le dossier assets configuré
            $filePath = ltrim($path, '/');
            
        } elseif (str_starts_with($path, '/assets/')) {
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
        
        // Cache 1 heure
        header('Cache-Control: public, max-age=3600'); 
        
        // Vérifier si le client a déjà le fichier en cache
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            
            if ($ifModifiedSince >= $lastModified) {
                // Not Modified
                http_response_code(304); 
                exit;
            }
        }

        // Lire et envoyer le contenu du fichier
        readfile($realPath);
        exit;
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
        
        // Au minimum ['', 'modules', 'nom', 'assets']
        if (count($pathParts) < 4) { 
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
}
