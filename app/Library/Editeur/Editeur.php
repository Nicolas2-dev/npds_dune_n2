<?php

namespace App\Library\Editeur;

use App\Library\Language\Language;


class Editeur
{

    /**
     * Charge et retourne l'éditeur WYSIWYG pour un textarea donné.
     *
     * Cette fonction génère le code HTML nécessaire pour afficher un éditeur riche.
     * 
     * @param string $Xzone Nom du textarea à éditer.
     * @param mixed $Xactiv Paramètre déprécié, utilisé uniquement si $Xzone = "custom" pour passer des options spécifiques.
     * @return string HTML de l'éditeur à afficher.
     */
    public static function affEditeur(string $Xzone, mixed $Xactiv): string
    {
        //global $language, $tmp_theme, $tiny_mce, $tiny_mce_theme, $tiny_mce_relurl;
        global $tiny_mce;

        $output = '';

        if (!$tiny_mce) {
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

                    include 'shared/tinymce/themes/advanced/npds.conf.php';

                    $output .= '});
                            });
                        //]]>
                        </script>';
                }
            } else {
                $output .= '<script type="text/javascript" src="shared/tinymce/tinymce.min.js"></script>';
            }
        } else {
            $tmp_Xzone .= $Xzone != 'custom' ? $Xzone . ',' : $Xactiv . ',';
        }

        return $output;
    }
}
