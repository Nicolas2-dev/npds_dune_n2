<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    ponent::rotateImg('http://site.com/img1.gif,http://site.com/img2.gif'); ?>
*/

class RotateImgComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        mt_srand((double)microtime() * 1000000);

        $arg = is_array($params) ? ($params[0] ?? '') : $params;
        $arg = Sanitize::argFilter($arg);

        $tab_img = explode(",", $arg);
        $imgnum = count($tab_img) > 1 ? mt_rand(0, count($tab_img) - 1) : (count($tab_img) == 1 ? 0 : -1);

        if ($imgnum != -1) {
            return '<img src="' . $tab_img[$imgnum] . '" border="0" alt="' . $tab_img[$imgnum] . '" title="' . $tab_img[$imgnum] . '" />';
        }

        return '';
    }
}
