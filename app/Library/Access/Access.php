<?php

namespace App\Library\Access;

use App\Library\Log\Log;


class Access
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
     * Affiche la page d'accès refusé et termine l'exécution.
     *
     * @return void
     */
    public function accessDenied(): void
    {
        $this->adminDie();
    }

    /**
     * Affiche une page d'accès refusé et termine l'exécution du script.
     *
     * Cette méthode inclut éventuellement le fichier de métadonnées 
     * `storage/meta/meta.php` si présent, puis affiche une carte Bootstrap
     * indiquant que l'accès est refusé dans plusieurs langues, et termine le script.
     *
     * @return void Ne retourne rien, termine le script avec die().
     */
    public function adminDie(): void
    {
        if (file_exists('storage/meta/meta.php')) {

            $Titlesitename = 'NPDS';

            include storage_path('meta/meta.php');
        }

        echo '<link id="bsth" rel="stylesheet" href="'. asset_url('shared/bootstrap/dist/css/bootstrap.min.css') .'" />
            </head>
            <body>
                <div class="contenair-fluid mt-5">
                    <div class= "card mx-auto p-3" style="width:380px; text-align:center">
                        <span style="font-size: 72px;">🚫</span>
                        <span class="text-danger h3 mb-3" style="">
                            Acc&egrave;s refus&eacute; ! <br />
                            Access denied ! <br />
                            Zugriff verweigert ! <br />
                            Acceso denegado ! <br />
                            &#x901A;&#x5165;&#x88AB;&#x5426;&#x8BA4; ! <br />
                        </span>
                        <hr />
                        <div>
                            <span class="text-body-secondary">NPDS - Portal System</span>
                            <img width="48px" class="adm_img ms-2" src="'. asset_url('images/admin/message_npds.png') .'" alt="icon_npds">
                        </div>
                    </div>
                </div>
            </body>
            </html>';

        die();
    }

    /**
     * Affiche une alerte d'administration pour des actions suspectes
     * et termine immédiatement le script.
     *
     * Cette méthode :
     *  - Supprime le cookie 'admin' et la variable globale $admin
     *  - Écrit un log de sécurité via Log::ecrireLog
     *  - Inclut le fichier 'storage/meta/meta.php'
     *  - Affiche un message d'alerte avec l'adresse IP enregistrée
     *  - Termine l'exécution du script avec die()
     *
     * @param string $motif Le motif ou la raison pour laquelle l'alerte est déclenchée
     *
     * @return void Cette fonction ne retourne rien et termine le script
     */
    public function AdminAlert(string $motif): void
    {
        global $admin;

        setcookie('admin', '', 0);
        unset($admin);

        Log::ecrireLog('security', 'auth.inc.php/Admin_alert : ' . $motif, '');

        if (file_exists('storage/meta/meta.php')) {

            $Titlesitename = 'NPDS';

            include storage_path('meta/meta.php');
        }

        echo '
            </head>
            <body>
                <br />
                <br />
                <br />
                <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;">
                    <strong>.: ' . translate('Votre adresse Ip est enregistrée') . ' :.</strong>
                </p>
            </body>
        </html>';
        die();
    }

}
