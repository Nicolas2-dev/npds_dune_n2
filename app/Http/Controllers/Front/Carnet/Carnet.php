<?php

namespace App\Http\Controllers\Front\Carnet;

use Npds\Config\Config;
use App\Support\Facades\Css;
use App\Support\Facades\Theme;
use App\Support\Facades\Encrypter;
use App\Http\Controllers\Core\FrontBaseController;


class Carnet extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        global $user; // global a revoir !

        if (!$user) {
            Header('Location: user.php');
        } else {
            $tmp_theme = Theme::getTheme();

            // Note : en attente de la refonte des fichier de configuration des themes
            include theme_path($tmp_theme . '/Config/Theme.php');

            // Note : en attente de la refonte de la gestion des métatags 
            $Titlesitename = translate('Carnet d\'adresses');

            include storage_path('meta/meta.php');

            echo '<link id="bsth" rel="stylesheet" href="' . asset_url('skins/default/bootstrap.min.css') . '" />';

            // Note : en attente de refonte de la gestion des assets 
            $language = Config::get('language.language');

            echo Css::importCss($tmp_theme, $language, '', '', '');

            // Note a revoir avec assetManager 
            include BASEPATH . 'assets/formhelp.java.php';

            $userX      = base64_decode($user);
            $userdata   = explode(':', $userX);

            $user_carnet = storage_path('users_private/' . $userdata[1] . '/mns/carnet.txt');

            echo '</head>
            <body class="p-4">';

            if (file_exists($user_carnet)) {
                $fp = fopen($user_carnet, 'r');

                if (filesize($user_carnet) > 0) {
                    $contents = fread($fp, filesize($user_carnet));
                }
                fclose($fp);

                if (substr($contents, 0, 5) != 'CRYPT') {
                    $fp = fopen($user_carnet, 'w');
                    fwrite($fp, 'CRYPT' . Encrypter::lEncrypt($contents));
                    fclose($fp);
                } else {
                    $contents = Encrypter::decryptK(substr($contents, 5), substr($userdata[2], 8, 8));
                }

                echo '<div class="row">';

                $contents = explode("\n", $contents);

                foreach ($contents as $tab) {
                    $tabi = explode(';', $tab);

                    if ($tabi[0] != '') {
                        echo '
                        <div class="border col-md-4 mb-1 p-3">
                            <a href="javascript: DoAdd(1,\'to_user\',\'' . $tabi[0] . ',\')";><b>' . $tabi[0] . '</b></a><br />
                            <a href="mailto:' . $tabi['1'] . '" >' . $tabi['1'] . '</a><br />
                            ' . $tabi['2'] . '
                        </div>';
                    }
                }

                echo '</div>';
            } else {
                echo '<div class="alert alert-secondary text-break">
                    <span>' . translate('Vous pouvez charger un fichier carnet.txt dans votre miniSite') . '.</span><br />
                    <span>' . translate('La structure de chaque ligne de ce fichier : nom_du_membre; adresse Email; commentaires') . '</span>
                </div>';
            }

            echo '</body>
            </html>';
        }
    }

}
