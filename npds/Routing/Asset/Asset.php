<?php

namespace Npds\Routing\Asset;

use Npds\Http\Request;
use Npds\Exceptions\Http\NotFoundHttpException;

class Asset
{
    /**
     * Instance unique de la classe Asset (singleton).
     *
     * @var Asset|null
     */
    protected static ?Asset $instance = null;

    /**
     * Racine du projet (au-dessus de public/).
     *
     * @var string
     */
    protected string $basePath;

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
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'txt'  => 'text/plain',
    ];

    /**
     * Constructeur : définit la racine du projet.
     */
    public function __construct()
    {
        // public/../ = racine projet
        $this->basePath = realpath(__DIR__ . '/../../../') ?: getcwd();
    }

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
     */
    public function setAssetsPath(string $path): void
    {
        $this->assetsPath = rtrim($path, '/');
    }

    public function setModulesPath(string $path): void
    {
        $this->modulesPath = rtrim($path, '/');
    }

    public function setThemesPath(string $path): void
    {
        $this->themesPath = rtrim($path, '/');
    }

    /**
     * Vérifie si la requête concerne un asset.
     */
    protected function isAssetRequest(string $path): bool
    {
        return preg_match('#/assets/#', $path) === 1 ||
               str_starts_with($path, '/modules/') ||
               str_starts_with($path, '/themes/');
    }

    /**
     * Sert un fichier asset.
     */
    protected function serveAsset(string $path): void
    {
        $filePath = null;

        $assetsBasePath = '/' . trim($this->assetsPath, '/');
        if (str_starts_with($path, $assetsBasePath)) {
            $filePath = $this->basePath . '/' . ltrim($path, '/');

        } elseif (str_starts_with($path, '/assets/')) {
            $relativePath = substr($path, 8);
            $possiblePaths = $this->getAssetPossiblePaths($relativePath);

            foreach ($possiblePaths as $testPath) {
                if (file_exists($testPath) && is_file($testPath)) {
                    $filePath = $testPath;
                    break;
                }
            }

        } elseif (str_starts_with($path, '/modules/')) {
            $filePath = $this->normalizeModuleThemePath($path, 'modules');

        } elseif (str_starts_with($path, '/themes/')) {
            $filePath = $this->normalizeModuleThemePath($path, 'themes');
        }

        if (!$filePath || !file_exists($filePath) || !is_file($filePath)) {
            throw new NotFoundHttpException('Asset not found');
        }

        $realPath = realpath($filePath);
        if (!$realPath || !$this->isPathSecure($realPath)) {
            throw new NotFoundHttpException('Asset not found');
        }

        $extension = pathinfo($realPath, PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes[$extension] ?? 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realPath));

        $lastModified = filemtime($realPath);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header('Cache-Control: public, max-age=3600');

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($ifModifiedSince >= $lastModified) {
                http_response_code(304);
                exit;
            }
        }

        readfile($realPath);
        exit;
    }

    /**
     * Normalise le chemin d'un module ou thème.
     */
    protected function normalizeModuleThemePath(string $path, string $type): ?string
    {
        if (strpos($path, '/assets/') === false) {
            return null;
        }

        $pathParts = explode('/', $path);

        if (count($pathParts) < 4) { 
            return null;
        }

        $moduleOrThemeName = $pathParts[2];
        $basePath = $this->basePath . '/' . ($type === 'modules' ? $this->modulesPath : $this->themesPath);

        $directPath = $this->basePath . '/' . ltrim($path, '/');
        if (file_exists($directPath)) {
            return $directPath;
        }

        $normalizedName = ucfirst(strtolower($moduleOrThemeName));
        $pathParts[2] = $normalizedName;
        $normalizedPath = $this->basePath . '/' . implode('/', array_slice($pathParts, 1));

        if (file_exists($normalizedPath)) {
            return $normalizedPath;
        }

        if (is_dir($basePath)) {
            $realDirs = array_filter(glob($basePath . '/*'), 'is_dir');

            foreach ($realDirs as $realDir) {
                $realDirName = basename($realDir);

                if (strtolower($realDirName) === strtolower($moduleOrThemeName)) {
                    $pathParts[2] = $realDirName;
                    $foundPath = $this->basePath . '/' . implode('/', array_slice($pathParts, 1));

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
     */
    protected function getAssetPossiblePaths(string $relativePath): array
    {
        $paths = [];

        $paths[] = $this->basePath . '/' . $this->assetsPath . '/' . ltrim($relativePath, '/');

        if (is_dir($this->basePath . '/' . $this->modulesPath)) {
            $modules = array_filter(glob($this->basePath . '/' . $this->modulesPath . '/*'), 'is_dir');
            foreach ($modules as $moduleDir) {
                $paths[] = $moduleDir . '/assets/' . ltrim($relativePath, '/');
            }
        }

        if (is_dir($this->basePath . '/' . $this->themesPath)) {
            $themes = array_filter(glob($this->basePath . '/' . $this->themesPath . '/*'), 'is_dir');
            foreach ($themes as $themeDir) {
                $paths[] = $themeDir . '/assets/' . ltrim($relativePath, '/');
            }
        }

        return $paths;
    }

    /**
     * Vérifie si le chemin est sécurisé.
     */
    protected function isPathSecure(string $realPath): bool
    {
        $assetsRealPath = realpath($this->basePath . '/' . $this->assetsPath);
        if ($assetsRealPath && str_starts_with($realPath, $assetsRealPath)) {
            return true;
        }

        $modulesRealPath = realpath($this->basePath . '/' . $this->modulesPath);
        if ($modulesRealPath && str_starts_with($realPath, $modulesRealPath)) {
            return strpos($realPath, '/assets/') !== false;
        }

        $themesRealPath = realpath($this->basePath . '/' . $this->themesPath);
        if ($themesRealPath && str_starts_with($realPath, $themesRealPath)) {
            return strpos($realPath, '/assets/') !== false;
        }

        return false;
    }
}
