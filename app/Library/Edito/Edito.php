<?php

namespace App\Library\Edito;

use App\Library\Date\Date;
use App\Library\Theme\Theme;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;


class Edito
{

    public static function affEdito()
    {
        list($affich, $Xcontents) = Edito::fabEdito();

        if (($affich) and ($Xcontents != '')) {
            $notitle = false;

            if (strstr($Xcontents, '!edito-notitle!')) {
                $notitle = 'notitle';
                $Xcontents = str_replace('!edito-notitle!', '', $Xcontents);
            }

            $ret = false;

            if (function_exists('themedito')) {
                $ret = Theme::themEdito($Xcontents);
            } else {
                if (function_exists('theme_centre_box')) {
                    $title = (!$notitle) ? translate('EDITO') : '';

                    theme_centre_box($title, $Xcontents);
                    $ret = true;
                }
            }

            if ($ret == false) {
                if (!$notitle) {
                    echo '<span class="edito">' . translate('EDITO') . '</span>';
                }

                echo $Xcontents;
                echo '<br />';
            }
        }
    }

    /**
     * Construit et retourne l'édito.
     *
     * Cette fonction lit le fichier d'édito approprié selon que l'utilisateur est connecté ou non,
     * gère l'affichage conditionnel basé sur les paramètres de jour/nuit et la durée de validité,
     * et retourne le contenu final formaté.
     *
     * @return array Tableau contenant :
     *               - bool $affich Indique si l'édito doit être affiché.
     *               - string $Xcontents Contenu de l'édito prêt à l'affichage.
     */
    public static function fabEdito(): array
    {
        global $cookie;

        if (isset($cookie[3])) {
            if (file_exists($path = 'storage/static/edito_membres.txt')) {
                $Xcontents = static::readFile($path);
            } else {
                if (file_exists($path = 'storage/static/edito.txt')) {
                    $Xcontents = static::readFile($path);
                }
            }
        } else {
            if (file_exists($path = 'storage/static/edito.txt')) {
                $Xcontents = static::readFile($path);
            }
        }

        $affich = false;
        $Xibid = strstr($Xcontents, 'aff_jours');

        if ($Xibid) {
            parse_str($Xibid, $Xibidout);

            if (($Xibidout['aff_date'] + ($Xibidout['aff_jours'] * 86400)) - time() > 0) {

                $affichJ = false;
                $affichN = false;

                if ((Date::nightDay() == 'Jour') and ($Xibidout['aff_jour'] == 'checked')) {
                    $affichJ = true;
                }

                if ((Date::nightDay() == 'Nuit') and ($Xibidout['aff_nuit'] == 'checked')) {
                    $affichN = true;
                }
            }

            $XcontentsT = substr($Xcontents, 0, strpos($Xcontents, 'aff_jours'));
            $contentJ = substr($XcontentsT, strpos($XcontentsT, '[jour]') + 6, strpos($XcontentsT, '[/jour]') - 6);
            $contentN = substr($XcontentsT, strpos($XcontentsT, '[nuit]') + 6, strpos($XcontentsT, '[/nuit]') - 19 - strlen($contentJ));

            $Xcontents = '';

            if (isset($affichJ) and $affichJ === true) {
                $Xcontents = $contentJ;
            }

            if (isset($affichN) and $affichN === true) {
                $Xcontents = $contentN != '' ? $contentN : $contentJ;
            }

            if ($Xcontents != '') {
                $affich = true;
            }
        } else {
            $affich = true;
        }

        $Xcontents = Metalang::metaLang(Language::affLangue($Xcontents));

        return [$affich, $Xcontents];
    }

    /**
     * Lit le contenu d'un fichier et retourne une chaîne vide si le fichier est vide ou introuvable.
     *
     * @param string $path Chemin complet du fichier à lire.
     * @return string Contenu du fichier, ou une chaîne vide si le fichier est vide ou ne peut être lu.
     */
    private static function readFile(string $path): string
    {
        if (is_readable($path) && filesize($path) > 0) {
            $fp = fopen($path, 'r');

            $content = fread($fp, filesize($path));

            fclose($fp);

            return $content ?: '';
        }

        return '';
    }
}
