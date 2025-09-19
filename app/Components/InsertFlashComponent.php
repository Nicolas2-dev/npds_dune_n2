<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/* 
Exemple d'appel :
    <?= Component::insertFlash('movie.swf', 600, 400, '#ffffff'); ?>
*/

class InsertFlashComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        [$name, $width, $height, $bgcol] = is_array($params) ? $params : [$params[0], $params[1], $params[2], $params[3]];

        return "<object codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" 
                        classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" 
                        width=\"$width\" height=\"$height\" id=\"$name\" align=\"middle\">
                    <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                    <param name=\"movie\" value=\"flash/$name\" />
                    <param name=\"quality\" value=\"high\" />
                    <param name=\"bgcolor\" value=\"$bgcol\" />
                    <embed src=\"flash/$name\" quality=\"high\" bgcolor=\"$bgcol\" width=\"$width\" height=\"$height\" 
                           name=\"$name\" align=\"middle\" allowScriptAccess=\"sameDomain\" 
                           type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                </object>";
    }
}
