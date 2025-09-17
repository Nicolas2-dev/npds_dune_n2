<?php

namespace App\Http\Controllers\Front\Modules;

use App\Http\Controllers\Core\FrontBaseController;

    // deprecated !

class Modules extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    // deprecated !

    function Access_Error()
    {
        include 'admin/die.php';
    }

    function filtre_module($strtmp)
    {
        if (
            strstr($strtmp, '..')
            || stristr($strtmp, 'script')
            || stristr($strtmp, 'cookie')
            || stristr($strtmp, 'iframe')
            || stristr($strtmp, 'applet')
            || stristr($strtmp, 'object')
        ) {
            Access_Error();
        } else {
            return $strtmp != '' ? true : false;
        }
    }

    Public function index()
    {
        if (filtre_module($ModPath) and filtre_module($ModStart)) {

            $isControllerAdmin = (strpos($ModPath, 'admin') !== false);

            if ($isControllerAdmin) {
                $pos = strpos($ModPath, '/admin');
                $ModPath = substr($ModPath, 0, $pos);
            }

            $controllerPath = $isControllerAdmin
                ? 'modules/' . $ModPath . '/http/controllers/admin/' . $ModStart . '.php'
                : 'modules/' . $ModPath . '/http/controllers/front/' . $ModStart . '.php';

            if (file_exists($controllerPath)) {
                include $controllerPath;
                exit;
            }

            Access_Error();

        } else {
            Access_Error();
        }
    }

}
