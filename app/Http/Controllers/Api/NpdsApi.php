<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Core\BaseController;


class NpdsApi extends BaseController
{

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        case 'alerte_api':
        case 'alerte_update':
            include 'npds_api.php';
            break;
        */

        parent::initialize();
    }


    function alerte_api()
    {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];

            $result = sql_query("SELECT * 
                                FROM " . sql_prefix('fonctions') . " 
                                WHERE fid='$id'");

            if (isset($result)) {
                $row = sql_fetch_assoc($result);

                if (count($row) > 0) {
                    $data = $row;
                }
            }

            echo json_encode($data);
        }
    }

    function alerte_update()
    {
        global $admin;

        $Xadmin = base64_decode($admin);
        $Xadmin = explode(':', $Xadmin);

        $aid = urlencode($Xadmin[0]);

        if (isset($_POST['id'])) {

            $id = $_POST['id'];

            $result = sql_query("SELECT * 
                                FROM " . sql_prefix('fonctions') . " 
                                WHERE fid=" . $id . "");

            $row = sql_fetch_assoc($result);

            $newlecture = $aid . '|' . $row['fdroits1_descr'];

            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fdroits1_descr='" . $newlecture . "' 
                    WHERE fid=" . $id . "");
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

}
