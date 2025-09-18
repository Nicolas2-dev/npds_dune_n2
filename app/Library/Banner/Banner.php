<?php

namespace App\Library\Banner;

use Npds\Config\Config;


class Banner
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    
    /**
     * Retourne l'instance singleton du dispatcher.
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
     * Génère et affiche l’en-tête HTML de la page, incluant les CSS et le header Bootstrap.
     *
     * Cette méthode charge les fichiers de configuration des modules et des métadonnées,
     * détermine la feuille de style appropriée selon la langue et le thème,
     * et construit le header HTML avec la barre de navigation et le titre de la page.
     *
     * @return void
     */
    public function headerPage(): void
    {
        // Note : en attente de refonte des fichier config des modules !
        include module_path('Upload/Config/Config.php');

        $Titlesitename  = Config::get('app.Titlesitename');

        // Note : en attente de refonte de la gestion des métatags
        include storage_path('meta/meta.php');

        if ($url_upload_css) {

            $language       = Config::get('language.language');

            $url_upload_cssX = str_replace('style.css', $language . '-style.css', $url_upload_css);

            if (is_readable($url_upload . $url_upload_cssX)) {
                $url_upload_css = $url_upload_cssX;
            }

            print('<link href="' . $url_upload . $url_upload_css . '" title="default" rel="stylesheet" type="text/css" media="all" />');
        }

        if (file_exists($path = app_path('Views/Bootstrap/Header_head.php'))) {
            include $path;
        }

        $Default_Theme  = Config::get('theme.Default_Theme');

        if (file_exists($path = theme_path($Default_Theme . '/Views/Bootstrape/Header_head.php'))) {
            include $path;
        }

        if (file_exists(theme_path($Default_Theme . '/assets/css/style.css'))) {
            echo '<link href="' . site_url('themes/' . $Default_Theme . '/assets/css/style.css') . '" rel="stylesheet" type="text/css" media="all" />';
        }

        echo '</head>
        <body style="margin-top:64px;">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg fixed-top bg-primary" data-bs-theme="dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="'. site_url('home') . '"><i class="fa fa-home fa-lg me-2"></i></a>
                        <span class="navbar-text">' . translate('Bannières - Publicité') . '</span>
                    </div>
                </nav>
                <h2 class="mt-4">' . translate('Bannières - Publicité') . ' @ ' . $Titlesitename . '</h2>
                <p align="center">';
    }

    /**
     * Génère et affiche le pied de page HTML de la page.
     *
     * Cette méthode inclut le footer Bootstrap et ferme les balises HTML ouvertes dans headerPage().
     *
     * @return void
     */
    public function footerPage(): void
    {
        include app_path('Views/Bootstrap/Footer_after.php');

        echo '</p>
                </div>
            </body>
        </html>';
    }
}
