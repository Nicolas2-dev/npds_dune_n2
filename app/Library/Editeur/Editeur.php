<?php

namespace App\Library\Editeur;


class Editeur
{

    #autodoc aff_editeur($Xzone, $Xactiv) : Charge l'éditeur ... ou non : $Xzone = nom du textarea / $Xactiv = deprecated <br /> si $Xzone="custom" on utilise $Xactiv pour passer des paramètres spécifiques
    function aff_editeur($Xzone, $Xactiv)
    {
        global $language, $tmp_theme, $tiny_mce, $tiny_mce_theme, $tiny_mce_relurl;

        $tmp = '';

        if ($tiny_mce) {
            static $tmp_Xzone;

            if ($Xzone == 'tiny_mce') {
                if ($Xactiv == 'end') {

                    if (substr((string) $tmp_Xzone, -1) == ',') {
                        $tmp_Xzone = substr_replace((string) $tmp_Xzone, '', -1);
                    }

                    if ($tmp_Xzone) {
                        $tmp = "<script type=\"text/javascript\">
                        //<![CDATA[
                            document.addEventListener(\"DOMContentLoaded\", function(e) {
                                tinymce.init({
                                selector: 'textarea.tin',
                                mobile: {menubar: true},
                                language : '" . language_iso(1, '', '') . "',";

                        include 'shared/tinymce/themes/advanced/npds.conf.php';

                        $tmp .= '});
                            });
                        //]]>
                        </script>';
                    }
                } else {
                    $tmp .= '<script type="text/javascript" src="shared/tinymce/tinymce.min.js"></script>';
                }
            } else {
                $tmp_Xzone .= $Xzone != 'custom' ? $Xzone . ',' : $Xactiv . ',';
            }
        } else {
            $tmp = '';
        }

        return $tmp;
    }

}
