<?php

namespace App\Http\Controllers\Admin\Modules;


use App\Http\Controllers\Core\AdminBaseController;


class Plugins extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();        
    }

    public function index()
    {
        include 'header.php';

        if ($ModPath != '') {

            $isControllerAdmin = (strpos($ModPath, 'admin') !== false) || (strpos($ModStart, 'admin') !== false);

            if ($isControllerAdmin) {
                $$ModPath = preg_replace('#^admin/#', '', $ModPath);
                $ModStart = preg_replace('#^admin/#', '', $ModStart);
            }

            $controllerPath = $isControllerAdmin
                ? 'modules/' . $ModPath . '/http/controllers/admin/' . $ModStart . '.php'
                : 'modules/' . $ModPath . '/http/controllers/front/' . $ModStart . '.php';

            if (file_exists($controllerPath)) {
                include $controllerPath;
            }
        } else {
            Url::redirectUrl(urldecode($ModStart));
        }
    }

}
