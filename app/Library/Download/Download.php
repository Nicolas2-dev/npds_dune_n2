<?php

namespace App\Library\Download;

use IntlDateFormatter;
use Npds\Config\Config;
use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Facades\Date;
use App\Support\Facades\Language;


class Download
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
     * Génère le bloc HTML pour les fichiers les plus téléchargés ou les derniers téléchargements.
     *
     * Cette fonction récupère les téléchargements depuis la base de données selon le critère
     * spécifié par `$ordre` (ex. 'dcounter' pour top downloads, 'ddate' pour derniers téléchargements),
     * tronque les noms de fichiers si nécessaire et applique les autorisations.
     *
     * @param string $form Type de formatage du rendu : 'short' ou autre (liste détaillée)
     * @param string $ordre Critère d'ordre : 'dcounter' ou 'ddate'
     * @return string HTML du bloc de téléchargement
     */
    public function topDownloadData(string $form, string $ordre): string
    {
        global $long_chain; // dans theme a revoir !

        if (!$long_chain) {
            $long_chain = 13;
        }

        $top = (int) Config::get('storie.top');

        $result = sql_query("SELECT did, dcounter, dfilename, dcategory, ddate, perms 
                            FROM " . sql_prefix('downloads') . " 
                            ORDER BY $ordre DESC 
                            LIMIT 0, $top");

        $lugar = 1;

        $ibid = '';

        while (list($did, $dcounter, $dfilename, $dcategory, $ddate, $dperm) = sql_fetch_row($result)) {
            if ($dcounter > 0) {
                $okfile = Auth::autorisation($dperm);

                if ($ordre == 'dcounter') {
                    $dd = Sanitize::wrh($dcounter);
                }

                if ($ordre == 'ddate') {
                    $dd = Date::formatTimes($ddate, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
                }

                $ori_dfilename = $dfilename;

                if (strlen($dfilename) > $long_chain) {
                    $dfilename = (substr($dfilename, 0, $long_chain)) . " ...";
                }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">
                            ' . $lugar . ' 
                            <a class="ms-2" href="download.php?op=geninfo&amp;did=' . $did . '&amp;out_template=1" title="' . $ori_dfilename . ' ' . $dd . '" data-bs-toggle="tooltip" >
                                ' . $dfilename . '
                            </a>
                            <span class="badge bg-secondary ms-auto align-self-center">' . $dd . '</span>
                        </li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1">
                            <a href="download.php?op=mydown&amp;did=' . $did . '" >
                                ' . $dfilename . '
                            </a> (' . translate('Catégorie') . ' : ' . Language::affLangue(stripslashes($dcategory)) . ')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">' . Sanitize::wrh($dcounter) . '</span>
                        </li>';
                    }
                }

                if ($okfile) {
                    $lugar++;
                }
            }
        }

        sql_free_result($result);

        return $ibid;
    }
}
