<?php

namespace App\Library\Download;

use IntlDateFormatter;


class Download
{

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
    public static function topDownloadData(string $form, string $ordre): string
    {
        global $top, $long_chain;

        if (!$long_chain) {
            $long_chain = 13;
        }

        $top = (int) $top;

        $result = sql_query("SELECT did, dcounter, dfilename, dcategory, ddate, perms 
                            FROM " . sql_prefix('downloads') . " 
                            ORDER BY $ordre DESC 
                            LIMIT 0, $top");

        $lugar = 1;

        $ibid = '';

        while (list($did, $dcounter, $dfilename, $dcategory, $ddate, $dperm) = sql_fetch_row($result)) {
            if ($dcounter > 0) {
                $okfile = autorisation($dperm);

                if ($ordre == 'dcounter') {
                    $dd = wrh($dcounter);
                }

                if ($ordre == 'ddate') {
                    $dd = formatTimes($ddate, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
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
                            </a> (' . translate('Catégorie') . ' : ' . affLangue(stripslashes($dcategory)) . ')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">' . wrh($dcounter) . '</span>
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
