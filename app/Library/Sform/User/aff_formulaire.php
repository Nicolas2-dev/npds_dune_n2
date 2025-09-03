<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/

use App\Library\Theme\Theme;

$m->addFormFieldSize(50);

settype($op, 'string');

if ($op != 'userinfo') {
    global $theme;

    $direktori = 'assets/images/forum/avatar';

    if (function_exists('theme_image')) {
        if (Theme::themeImage('forum/avatar/blank.gif')) {
            $direktori = 'themes/' . $theme . '/assets/images/forum/avatar';
        }
    }

    $m->addExtra('<img class="img-thumbnail n-ava mb-2" src="' . $direktori . '/' . $user_avatar . '" align="top" title="" />');
}

if (($op == 'userinfo') and ($user)) {
    global $act_uname;
    $act_uname = "<a href='powerpack.php?op=instant_message&amp;to_userid=$uname' title='" . translate('Envoyer un message interne') . "'>$uname</a>";

    $m->addField('act_uname', translate('ID utilisateur (pseudo)'), $act_uname, 'text', true, 25, '', '');
} else {
    $m->addField('uname', translate('ID utilisateur (pseudo)'), $uname, 'text', true, 25, '', '');
}

if ($name != '') {
    $m->addField('name', translate('Identité'), $name, 'text', false, 60, '', '');
}

if ($email != '') {
    $m->addField('email', translate('Véritable adresse Email'), $email, 'text', true, 60, '', '');
}

// if ($user_viewemail === 1) {
//    $checked = true; 
//} else {
//    $checked = false;
//}

// $m->addCheckbox('user_viewemail',translate('Allow other users to view my email address'), 1, false, $checked);

settype($url, 'string');

if ($url != '') {
    $url = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    $m->addField('url',  translate('Page d\'accueil'), $url, 'text', false, 100, '', '');
}

if ($user_from != '') {
    $m->addField('user_from', translate('Localisation'), $user_from, 'text', false, 100, '', '');
}

if ($user_occ != '') {
    $m->addField('user_occ', translate('Votre activité'), $user_occ, 'text', false, 100, '', '');
}

if ($user_intrest != '') {
    $m->addField('user_intrest', translate('Centres d\'interêt'), $user_intrest, 'text', false, 150, '', '');
}

if ($op == 'userinfo' and $bio != '') {
    $m->addField('bio', translate('Informations supplémentaires'), $bio, 'textarea', false, 255, 7, '', '');
}

if ($op != 'userinfo') {
    if ($user_sig != '') {
        $m->addField('user_sig', translate('Signature'), StripSlashes($user_sig), 'textarea', false, 255, '', '');
    }
}


// !!! à revoir !! pour prise en compte du champ choisi dans user_extend
settype($C7, 'float');
settype($C8, 'float');

if ($C7 != '') {
    $m->addField('C7', 'Latitude', $C7, 'text', false, 100, '', '', '');
}

if ($C8 != '') {
    $m->addField('C8', 'Longitude', $C8, 'text', false, 100, '', '', '');
}
