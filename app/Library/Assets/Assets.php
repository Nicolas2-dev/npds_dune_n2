<?php

namespace App\Library\Assets;

use App\Support\Facades\Theme;
use Npds\Support\Facades\Event;


/**
 * Gestionnaire centralisé des ressources (CSS, JS, Favicons).
 * Permet l'ajout, la minification, le versioning et la distribution
 * des assets selon leur zone (header, footer, inline).
 */
class Assets
{

    /**
     * Instance unique (singleton) du gestionnaire d’assets.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Zones disponibles pour le dispatch automatique via les événements.
     * Chaque zone référence une ou plusieurs méthodes de rendu.
     *
     * @var array<string, string[]>
     */

    /**
     * 
     *
     * @var [type]
     */
    protected array $zones = [
        'assets.css'        => ['dispatchCss', 'dispatchCssInline'],
        'assets.header.js'  => ['dispatchJsHeader', 'dispatchJsInlineHeader'],
        'assets.footer.js'  => ['dispatchJsFooter', 'dispatchJsInlineFooter'],
        'assets.favico'     => ['dispatchFavico'], 
    ];

    /** 
     * @var array<int, array<string,mixed>> Liste des fichiers CSS 
     */
    protected array $css = [];

    /** 
     * @var array<int, array<string,mixed>> Liste des CSS inline 
     */
    protected array $css_inline = [];

    /** 
     * @var array<int, array<string,mixed>> Liste des fichiers JS à injecter dans le header 
     */
    protected array $js_header = [];

    /** 
     * @var array<int, array<string,mixed>> Liste des fichiers JS à injecter dans le footer 
     */
    protected array $js_footer = [];

    /** 
     * @var array<int, array<string,mixed>> Liste des JS inline dans le header 
     */
    protected array $js_inline_header = [];

    /** 
     * @var array<int, array<string,mixed>> Liste des JS inline dans le footer 
     */
    protected array $js_inline_footer = [];

    /**
     * Tableau de dépendances entre assets (ex: jQuery -> Bootstrap).
     *
     * @var array<string, string[]>
     */
    protected array $dependencies = [];


    /**
     * Retourne l'instance unique du gestionnaire d’assets.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Ajoute un fichier CSS à la file d’attente.
     *
     * @param string      $path      Chemin relatif du fichier CSS
     * @param string[]    $dependsOn Liste des dépendances éventuelles
     * @param string|null $uri       URI spécifique où charger le CSS
     */
    public function addCss(string $path, array $dependsOn = [], ?string $uri = null): void
    {
        $this->addAsset($this->css, $path, $dependsOn, $uri);
    }

    /**
     * Ajoute un bloc CSS inline (dans une balise <style>).
     *
     * @param string      $code Code CSS
     * @param string|null $uri  URI spécifique où injecter le CSS
     */
    public function addCssInline(string $code, ?string $uri = null): void
    {
        $this->css_inline[] = ['code' => $code, 'uri' => $uri];
    }

    /**
     * Ajoute un fichier JavaScript à injecter dans le header.
     *
     * @param string      $path      Chemin relatif du fichier JS
     * @param string[]    $dependsOn Liste des dépendances éventuelles
     * @param string|null $uri       URI spécifique où charger le JS
     */
    public function addJsHeader(string $path, array $dependsOn = [], ?string $uri = null): void
    {
        $this->addAsset($this->js_header, $path, $dependsOn, $uri);
    }

    /**
     * Ajoute un fichier JavaScript à injecter dans le footer.
     *
     * @param string      $path      Chemin relatif du fichier JS
     * @param string[]    $dependsOn Liste des dépendances éventuelles
     * @param string|null $uri       URI spécifique où charger le JS
     */
    public function addJsFooter(string $path, array $dependsOn = [], ?string $uri = null): void
    {
        $this->addAsset($this->js_footer, $path, $dependsOn, $uri);
    }

    /**
     * Ajoute un bloc JavaScript inline dans le header.
     *
     * @param string      $code Code JavaScript
     * @param string|null $uri  URI spécifique où injecter le JS
     */
    public function addJsInlineHeader(string $code, ?string $uri = null): void
    {
        $this->js_inline_header[] = ['code' => $code, 'uri' => $uri];
    }

    /**
     * Ajoute un bloc JavaScript inline dans le footer.
     *
     * @param string      $code Code JavaScript
     * @param string|null $uri  URI spécifique où injecter le JS
     */
    public function addJsInlineFooter(string $code, ?string $uri = null): void
    {
        $this->js_inline_footer[] = ['code' => $code, 'uri' => $uri];
    }

    /**
     * Distribue les fichiers CSS enregistrés.
     */
    public function dispatchCss(): void
    {
        $this->dispatch($this->css, 'css');
    }

    /**
     * Distribue les blocs CSS inline enregistrés.
     */
    public function dispatchCssInline(): void
    {
        $this->dispatchInline($this->css_inline, 'css');
    }

    /**
     * Distribue les fichiers JS du header enregistrés.
     */
    public function dispatchJsHeader(): void
    {
        $this->dispatch($this->js_header, 'js');
    }

    /**
     * Distribue les fichiers JS du footer enregistrés.
     */
    public function dispatchJsFooter(): void
    {
        $this->dispatch($this->js_footer, 'js');
    }

    /**
     * Distribue les blocs JS inline du header.
     */
    public function dispatchJsInlineHeader(): void
    {
        $this->dispatchInline($this->js_inline_header, 'js');
    }

    /**
     * Distribue les blocs JS inline du footer.
     */
    public function dispatchJsInlineFooter(): void
    {
        $this->dispatchInline($this->js_inline_footer, 'js');
    }

    /**
     * Enregistre les gestionnaires de zones dans le système d’événements.
     */
    public function register(): void
    {
        foreach ($this->zones as $zone => $handlers) {
            foreach ($handlers as $handler) {
                Event::listen($zone, function () use ($handler) {
                    $this->$handler();
                });
            }
        }
    }

    /**
     * Distribue les balises favicon (fallback si non présent dans le thème).
     */
    public function dispatchFavico(): void
    {
        $theme = Theme::getTheme();

        $favicoPath = theme_path($theme . '/assets/images/favicon/favicon.ico');

        $favicoUrl = file_exists($favicoPath)
            ? site_url('themes/' . $favicoPath)
            : asset_url('images/favicon/favicon.ico');

        echo '<link rel="shortcut icon" href="' . $favicoUrl . '" type="image/x-icon">' . PHP_EOL;
        echo '<link rel="apple-touch-icon" sizes="120x120" href="' . asset_url('images/favicon/favicon-120.png') . '">' . PHP_EOL;
        echo '<link rel="apple-touch-icon" sizes="152x152" href="' . asset_url('images/favicon/favicon-152.png') . '">' . PHP_EOL;
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . asset_url('images/favicon/favicon-180.png') . '">' . PHP_EOL;
    }

    /**
     * Ajoute un asset dans un conteneur donné, en gérant ses dépendances.
     *
     * @param array<int,array<string,mixed>> $container Conteneur d’assets
     * @param string                         $path      Chemin relatif de l’asset
     * @param string[]                       $dependsOn Liste des dépendances
     * @param string|null                    $uri       URI spécifique
     */
    protected function addAsset(array &$container, string $path, array $dependsOn = [], ?string $uri = null): void
    {
        foreach ($container as $asset) {
            if ($asset['path'] === $path) return;
        }

        $container[] = [
            'path'       => $path,
            'dependsOn'  => $dependsOn,
            'uri'        => $uri,
        ];

        $this->dependencies[$path] = $dependsOn;
    }

    /**
     * Affiche les balises HTML (<link> ou <script>) pour une liste d’assets.
     *
     * @param array<int,array<string,mixed>> $assets Liste des assets
     * @param string                         $type   Type d’asset ("css" ou "js")
     */
    protected function dispatch(array $assets, string $type): void
    {
        $assets = $this->filterByUri($assets);
        $files = array_map(fn($a) => $a['path'], $assets);

        foreach ($this->resolveDependencies($files) as $file) {
            if ($type === 'css') {
                echo '<link rel="stylesheet" href="' . $this->assetUrl($file) . '" />' . PHP_EOL;
            } else {
                echo '<script type="text/javascript" src="' . $this->assetUrl($file) . '"></script>' . PHP_EOL;
            }
        }
    }

    /**
     * Affiche un bloc inline combiné et minifié (<style> ou <script>).
     *
     * @param array<int,array<string,mixed>> $assets Liste des assets inline
     * @param string                         $type   Type d’asset ("css" ou "js")
     */
    protected function dispatchInline(array $assets, string $type): void
    {
        $assets = $this->filterByUri($assets);
        if (empty($assets)) return;

        $combined = implode("\n", array_column($assets, 'code'));

        if ($type === 'css') {
            echo '<style type="text/css">' . $this->minifyCss($combined) . '</style>' . PHP_EOL;
        } else {
            echo '<script type="text/javascript">' . $this->minifyJs($combined) . '</script>' . PHP_EOL;
        }
    }

    /**
     * Filtre les assets selon l’URI courante.
     *
     * @param array<int,array<string,mixed>> $assets Liste brute des assets
     *
     * @return array<int,array<string,mixed>> Liste filtrée
     */
    protected function filterByUri(array $assets): array
    {
        $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        return array_filter($assets, fn($a) => empty($a['uri']) || $a['uri'] === $currentUri);
    }

    /**
     * Résout les dépendances entre fichiers pour garantir l’ordre correct.
     *
     * @param string[] $files Liste des fichiers à traiter
     *
     * @return string[] Liste triée des fichiers
     */
    protected function resolveDependencies(array $files): array
    {
        $resolved = [];
        $seen = [];

        $resolve = function ($file) use (&$resolve, &$resolved, &$seen) {
            if (isset($seen[$file])) return;
            $seen[$file] = true;

            foreach ($this->dependencies[$file] ?? [] as $dep) {
                $resolve($dep);
            }

            if (!in_array($file, $resolved, true)) {
                $resolved[] = $file;
            }
        };

        foreach ($files as $file) {
            $resolve($file);
        }

        return $resolved;
    }

    /**
     * Retourne l’URL publique d’un asset avec versioning par date de modification.
     *
     * @param string $file Chemin relatif du fichier
     *
     * @return string URL versionnée de l’asset
     */
    protected function assetUrl(string $file): string
    {
        // Chemin physique selon qu'on soit dans un thème ou dans assets classique
        
        $isTheme = str_starts_with($file, 'themes/');
        
        if ($isTheme) {
            $path = THEME_PATH . $file; 
        } else {
            $path = BASEPATH . 'assets/' . $file;
        }

        if (file_exists($path)) {
            $version = filemtime($path);

            return $isTheme ? site_url($file . '?v=' . $version) : asset_url($file . '?v=' . $version);
        }

        return $isTheme ? site_url($file) : asset_url($file);
    }

    /**
     * Minifie du code CSS.
     *
     * @param string $css Code CSS brut
     *
     * @return string Code CSS minifié
     */
    protected function minifyCss(string $css): string
    {
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = str_replace([' {', '{ '], '{', $css);
        $css = str_replace([' }', '} '], '}', $css);
        $css = str_replace([' ;', '; '], ';', $css);
        $css = str_replace(': ', ':', $css);

        return trim($css);
    }

    /**
     * Minifie du code JavaScript inline.
     *
     * @param string $js Code JS brut
     *
     * @return string Code JS minifié
     */
    protected function minifyJs(string $js): string
    {
        $js = preg_replace_callback(
            '#("(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|`(?:\\\\.|[^`\\\\])*`)|(/\*.*?\*/)#s',
            fn($m) => isset($m[2]) ? '' : $m[1],
            $js
        );

        $js = preg_replace_callback(
            '#("(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|`(?:\\\\.|[^`\\\\])*`)|(//.*?$)#m',
            fn($m) => isset($m[2]) ? '' : $m[1],
            $js
        );

        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/ ?([{};,:]) ?/', '$1', $js);

        return trim($js);
    }

}
