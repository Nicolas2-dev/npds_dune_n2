<?php

namespace App\Library\Log;


class Log
{

    #autodoc Ecr_Log($fic_log, $req_log, $mot_log) : Pour &eacute;crire dans un log (security.log par exemple)
    function Ecr_Log($fic_log, $req_log, $mot_log)
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
            $mot_log = 'IP=>' . getip();
        }

        $ibid = sprintf("%-10s %-60s %-10s\r\n", date('d/m/Y H:i:s', time()), basename($_SERVER['PHP_SELF']) . '=>' . strip_tags(urldecode($req_log)), strip_tags(urldecode($mot_log))); //pourquoi urldecode ici ?

        fwrite($fp, $ibid);
        flock($fp, 3);
        fclose($fp);
    }
    
}
