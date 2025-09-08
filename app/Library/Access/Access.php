<?php

namespace App\Library\Access;

use App\Library\Log\Log;


class Access
{

    /**
     * Affiche la page d'accès refusé et termine l'exécution.
     *
     * @return void
     */
    public static function accessDenied(): void
    {
        include 'admin/die.php';
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
    public static function AdminAlert(string $motif): void
    {
        global $admin;

        setcookie('admin', '', 0);
        unset($admin);

        Log::ecrireLog('security', 'auth.inc.php/Admin_alert : ' . $motif, '');

        include storage_path('meta/meta.php');

        echo '
            </head>
            <body>
                <br /><br /><br />
                <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;">
                    <strong>.: ' . translate('Votre adresse Ip est enregistrée') . ' :.</strong>
                </p>
            </body>
        </html>';
        die();
    }

}
