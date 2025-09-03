<?php

namespace App\Library\Log;

use App\Library\Http\Request;


class Log
{

    /**
     * Écrit dans un fichier de log.
     *
     * @param string $fic_log Nom du fichier de log (ex: "security" pour security.log)
     * @param string $req_log Description de l'action ou de l'information à logger
     * @param string $mot_log Informations supplémentaires ; si vide, l'IP est enregistrée
     * @return void
     */
    public static function ecrireLog(string $fic_log, string $req_log, string $mot_log): void
    {
        // $Fic_log= the file name :
        //  => "security" for security maters
        //  => ""
        // $req_log= a phrase describe the infos
        //
        // $mot_log= if "" the Ip is recorded, else extend status infos

        $logfile = 'storage/logs/' . $fic_log . '.log';

        $fp = fopen($logfile, 'a');
        flock($fp, 2);
        fseek($fp, filesize($logfile));

        if ($mot_log == '') {
            $mot_log = 'IP=>' . Request::getip();
        }

        $ibid = sprintf("%-10s %-60s %-10s\r\n", date('d/m/Y H:i:s', time()), basename($_SERVER['PHP_SELF']) . '=>' . strip_tags(urldecode($req_log)), strip_tags(urldecode($mot_log))); //pourquoi urldecode ici ?

        fwrite($fp, $ibid);
        flock($fp, 3);
        fclose($fp);
    }
}
