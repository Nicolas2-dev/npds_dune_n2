<?php

namespace App\Components;

use App\Support\Facades\Theme;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
    <?= Component::forumIcones(); ?>
*/

class ForumIconesComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        if ($ibid = Theme::themeImage("forum/icons/red_folder.gif")) {
            $imgtmpR = $ibid;
        } else {
            $imgtmpR = "images/forum/icons/red_folder.gif";
        }

        if ($ibid = Theme::themeImage("forum/icons/folder.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = "images/forum/icons/folder.gif";
        }

        $ibid = "<img src=\"$imgtmpR\" border=\"\" alt=\"\" /> = " . translate("Les nouvelles contributions depuis votre dernière visite.") . "<br />";
        $ibid .= "<img src=\"$imgtmp\" border=\"\" alt=\"\" /> = " . translate("Aucune nouvelle contribution depuis votre dernière visite.");

        return $ibid;
    }
}