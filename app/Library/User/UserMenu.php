<?php

namespace App\Library\User;


class UserMenu
{

    #autodoc member_menu($mns,$qui) : retourne un menu utilisateur 
    function member_menu($mns, $qui)
    {
        global $op;

        $ed_u = $op == 'edituser' ? 'active' : '';
        $cl_edj = $op == 'editjournal' ? 'active' : '';
        $cl_edh = $op == 'edithome' ? 'active' : '';
        $cl_cht = $op == 'chgtheme' ? 'active' : '';
        $cl_edjh = ($op == 'editjournal' or $op == 'edithome') ? 'active' : '';
        $cl_u = $_SERVER['REQUEST_URI'] == '/user.php' ? 'active' : '';
        $cl_pm = strstr($_SERVER['REQUEST_URI'], '/viewpmsg.php') ? 'active' : '';
        $cl_rs = ($_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux' or $_SERVER['QUERY_STRING'] == 'ModPath=reseaux-sociaux&ModStart=reseaux-sociaux&op=EditReseaux') ? 'active' : '';

        echo '<ul class="nav nav-tabs d-flex flex-wrap"> 
            <li class="nav-item"><a class="nav-link ' . $cl_u . '" href="user.php" title="' . translate('Votre compte') . '" data-bs-toggle="tooltip" ><i class="fas fa-user fa-2x d-xl-none"></i><span class="d-none d-xl-inline"><i class="fas fa-user fa-lg"></i></span></a></li>
            <li class="nav-item"><a class="nav-link ' . $ed_u . '" href="user.php?op=edituser" title="' . translate('Vous') . '" data-bs-toggle="tooltip" ><i class="fas fa-user-edit fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate('Vous') . '</span></a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass ' . $cl_edjh . '" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" data-bs-html="true" title="' . translate('Editer votre journal') . '<br />' . translate('Editer votre page principale') . '"><i class="fas fa-edit fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">Editer</span></a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item ' . $cl_edj . '" href="user.php?op=editjournal" title="' . translate('Editer votre journal') . '" data-bs-toggle="tooltip">' . translate('Journal') . '</a></li>
                    <li><a class="dropdown-item ' . $cl_edh . '" href="user.php?op=edithome" title="' . translate('Editer votre page principale') . '" data-bs-toggle="tooltip">' . translate('Page') . '</a></li>
                </ul>
            </li>';

        include 'modules/upload/config/config.php';

        if (($mns) and ($autorise_upload_p)) {
            include_once 'modules/blog/support/upload_minisite.php';

            $PopUp = win_upload('popup');

            echo '<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle tooltipbyclass" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="' . translate('Gérer votre miniSite') . '"><i class="fas fa-desktop fa-2x d-xl-none me-2"></i><span class="d-none d-xl-inline">' . translate('MiniSite') . '</span></a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="minisite.php?op=' . $qui . '" target="_blank">' . translate('MiniSite') . '</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="window.open(' . $PopUp . ')" >' . translate('Gérer votre miniSite') . '</a></li>
                </ul>
            </li>';
        }

        echo '<li class="nav-item"><a class="nav-link ' . $cl_cht . '" href="user.php?op=chgtheme" title="' . translate('Changer le thème') . '"  data-bs-toggle="tooltip" ><i class="fas fa-paint-brush fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate('Thème') . '</span></a></li>
            <li class="nav-item"><a class="nav-link ' . $cl_rs . '" href="modules.php?ModPath=reseaux-sociaux&amp;ModStart=reseaux-sociaux" title="' . translate('Réseaux sociaux') . '"  data-bs-toggle="tooltip" ><i class="fas fa-share-alt-square fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate('Réseaux sociaux') . '</span></a></li>
            <li class="nav-item"><a class="nav-link ' . $cl_pm . '" href="viewpmsg.php" title="' . translate('Message personnel') . '"  data-bs-toggle="tooltip" ><i class="far fa-envelope fa-2x d-xl-none"></i><span class="d-none d-xl-inline">&nbsp;' . translate('Message') . '</span></a></li>
            <li class="nav-item"><a class="nav-link " href="user.php?op=logout" title="' . translate('Déconnexion') . '" data-bs-toggle="tooltip" ><i class="fas fa-sign-out-alt fa-2x text-danger d-xl-none"></i><span class="d-none d-xl-inline text-danger">&nbsp;' . translate('Déconnexion') . '</span></a></li>
        </ul>
        <div class="mt-3"></div>';
    }

}
