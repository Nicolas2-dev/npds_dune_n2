<?php

namespace App\Library\Editeur;

use Npds\Config\Config;
use App\Support\Facades\Language;


class Editeur
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
     * Charge et retourne l'éditeur WYSIWYG pour un textarea donné.
     *
     * Cette fonction génère le code HTML nécessaire pour afficher un éditeur riche.
     * 
     * @param string $Xzone Nom du textarea à éditer.
     * @param mixed $Xactiv Paramètre déprécié, utilisé uniquement si $Xzone = "custom" pour passer des options spécifiques.
     * @return string HTML de l'éditeur à afficher.
     */
    public function affEditeur(string $Xzone, mixed $Xactiv): string
    {
        //global $language, $tmp_theme, $tiny_mce, $tiny_mce_theme, $tiny_mce_relurl; // note a revoir !

        $output = '';

        if (!Config::get('editeur.tiny_mce')) {
            return $output;
        }

        static $tmp_Xzone;

        if ($Xzone == 'tiny_mce') {
            if ($Xactiv == 'end') {

                if (substr((string) $tmp_Xzone, -1) == ',') {
                    $tmp_Xzone = substr_replace((string) $tmp_Xzone, '', -1);
                }

                if ($tmp_Xzone) {
                    $output = "<script type=\"text/javascript\">
                        //<![CDATA[
                            document.addEventListener(\"DOMContentLoaded\", function(e) {
                                tinymce.init({
                                selector: 'textarea.tin',
                                mobile: {menubar: true},
                                language : '" . Language::languageIso(1, '', '') . "',";
                    // a revoir pour integrer les config dans un fichier config pour lib config !
                    include 'shared/tinymce/themes/advanced/npds.conf.php';

                    $output .= '});
                            });
                        //]]>
                        </script>';
                }
            } else {
                // probleme non pris en compte par le routeur a revoir !
                $output .= '<script type="text/javascript" src="shared/tinymce/tinymce.min.js"></script>';
            }
        } else {
            $tmp_Xzone .= $Xzone != 'custom' ? $Xzone . ',' : $Xactiv . ',';
        }

        return $output;
    }

    /**
     * Démarre l'éditeur TinyMCE si l'initialisation globale est active.
     *
     * Cette méthode vérifie la variable globale `$tiny_mce_init` et, 
     * si elle est définie, affiche le code nécessaire pour initialiser 
     * l'éditeur au début de la page.
     *
     * @return void
     */
    public function start()
    {
        global $tiny_mce_init; // retour de page ref debile et pas de coherence avec le status si editeur true ou false 

        if ($tiny_mce_init) {
            echo $this->affEditeur('tiny_mce', 'begin');
        }
    }

    /**
     * Termine l'éditeur TinyMCE si activé dans la configuration.
     *
     * Cette méthode vérifie la configuration de l'application (`editeur.tiny_mce`) 
     * et, si l'éditeur est activé, affiche le code de fermeture de l'éditeur.
     *
     * @return void
     */
    public function end()
    { 
        if (Config::get('editeur.tiny_mce')) {
            echo $this->affEditeur('tiny_mce', 'end');
        }
    }
}
