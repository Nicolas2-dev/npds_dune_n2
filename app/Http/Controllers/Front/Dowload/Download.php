<?php

namespace App\Http\Controllers\Front\Download;

use Npds\Config\Config;
use App\Support\Facades\Auth;
use App\Support\Security\Hack;
use App\Support\Facades\Mailer;
use Npds\Support\Facades\Request;
use App\Library\Download\DownloadTrait;
use App\Library\Download\Download as LDownload;
use App\Http\Controllers\Core\FrontBaseController;


class Download extends FrontBaseController
{

    use DownloadTrait;

    
    protected int $pdst = 1;

    protected $download;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        $this->download = LDownload::getInstance(); 

        parent::initialize();
    }    

    public function main()
    {
        global $dcategory, $sortby, $sortorder; // global a revoir !

        echo '<h2>' . translate('Chargement de fichiers') . '</h2>
        <hr />';

        $this->download->list();

        $dcategory  = Hack::removeHack($this->download->sanitizeCategory($dcategory)); 
        $dcategory  = str_replace("&#039;", "\'", $dcategory);

        if ($dcategory != translate('Aucune catégorie')) {

            $sortby = Hack::removeHack($this->download->sanitizeCategory($sortby));

            $this->download->listDownloads($dcategory, $sortby, $sortorder);
        }

        if (file_exists('storage/static/download.ban.txt')) {
            include 'storage/static/download.ban.txt';
        }
    }

    public function transferFile(int $did)
    {
        $result = sql_query("SELECT dcounter, durl, perms 
                            FROM " . sql_prefix('downloads') . " 
                            WHERE did='$did'");

        list($dcounter, $durl, $dperm) = sql_fetch_row($result);

        if (!$durl) {
            echo '<h2>' . translate('Chargement de fichiers') . '</h2>
            <hr />
            <div class="lead alert alert-danger">' . translate('Ce fichier n\'existe pas ...') . '</div>';

        } else {
            if (stristr($dperm, ',')) {
                $ibid = explode(',', $dperm);

                foreach ($ibid as $v) {
                    $aut = true;

                    if (Auth::autorisation($v) == true) {
                        $dcounter++;

                        sql_query("UPDATE " . sql_prefix('downloads') . " 
                                SET dcounter='$dcounter' 
                                WHERE did='$did'");

                        header('location: ' . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
                        break;
                    } else {
                        $aut = false;
                    }
                }

                if ($aut == false) {
                    Header('Location: download.php');
                }
            } else {
                if (Auth::autorisation($dperm)) {
                    $dcounter++;

                    sql_query("UPDATE " . sql_prefix('downloads') . " 
                            SET dcounter='$dcounter' 
                            WHERE did='$did'");

                    header('location: ' . str_replace(basename($durl), rawurlencode(basename($durl)), $durl));
                } else {
                    Header('Location: download.php');
                }
            }
        }
    }

    public function broken(int $did)
    {
        global $user, $cookie; // global a revoir !

        if ($user) {
            if ($did) {

                $message = Config::get('app.nuke_url') . "\n"
                     . translate('Téléchargements') . " ID : $did\n"
                     . translate('Auteur') . " $cookie[1] / IP : " . Request::getip() . "\n\n";

                $message .= Config::get('signature.signature');

                Mailer::sendEmail(
                    Config::get('mailer.notify_email'), 
                    html_entity_decode(translate('Rapporter un lien rompu'), ENT_COMPAT | ENT_HTML401, 'UTF-8'), 
                    nl2br($message), 
                    Config::get('mailer.notify_from'), 
                    false, 
                    'html', 
                    ''
                );

                echo '<div class="alert alert-success">
                    <p class="lead">
                        ' . translate('Pour des raisons de sécurité, votre nom d\'utilisateur et votre adresse IP vont être momentanément conservés.') . '
                        <br />' . translate('Merci pour cette information. Nous allons l\'examiner dès que possible.') . '
                    </p>
                </div>';

            } else {
                Header('Location: download.php');
            }
        } else {
            Header('Location: download.php');
        }
    }

    /**
     * Méthodes importées via DownloadTrait :
     * - genInfo(int $did, int $out_template)
     */

}

/*
switch ($op) {
    case 'main':
        main();
        break;

    case 'mydown':
        transferfile($did);
        break;

    case 'geninfo':
        geninfo($did, $out_template);
        break;

    case 'broken':
        broken($did);
        break;

    default:
        main();
        break;
}
*/
