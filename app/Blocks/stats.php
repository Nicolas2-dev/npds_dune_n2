<?php

if (! function_exists('Site_Activ')) {
    #autodoc:<Mainfile.php>
    #autodoc <span class="text-success">BLOCS NPDS</span>:
    #autodoc Site_Activ() : Bloc activité du site <br />=> syntaxe : function#Site_Activ
    function Site_Activ()
    {
        global $startdate, $top;

        list($membres, $totala, $totalb, $totalc, $totald, $totalz) = reqStat();

        $aff = '<p class="text-center">' . translate('Pages vues depuis') . ' ' . $startdate . ' : <span class="fw-semibold">' . wrh($totalz) . '</span></p>
            <ul class="list-group mb-3" id="site_active">
            <li class="my-1">' . translate('Nb. de membres') . ' <span class="badge rounded-pill bg-secondary float-end">' . wrh(($membres)) . '</span></li>
            <li class="my-1">' . translate('Nb. d\'articles') . ' <span class="badge rounded-pill bg-secondary float-end">' . wrh($totala) . '</span></li>
            <li class="my-1">' . translate('Nb. de forums') . ' <span class="badge rounded-pill bg-secondary float-end">' . wrh($totalc) . '</span></li>
            <li class="my-1">' . translate('Nb. de sujets') . ' <span class="badge rounded-pill bg-secondary float-end">' . wrh($totald) . '</span></li>
            <li class="my-1">' . translate('Nb. de critiques') . ' <span class="badge rounded-pill bg-secondary float-end">' . wrh($totalb) . '</span></li>
            </ul>';

        if ($ibid = themeImage('box/top.gif')) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        }

        if ($imgtmp) {
            $aff .= '<p class="text-center">
                <a href="top.php">
                    <img src="' . $imgtmp . '" alt="' . translate('Top') . ' ' . $top . '" />
                </a>&nbsp;&nbsp;';

            if ($ibid = themeImage('box/stat.gif')) {
                $imgtmp = $ibid;
            } else {
                $imgtmp = false;
            }

            $aff .= '<a href="stats.php">
                    <img src="' . $imgtmp . '" alt="' . translate('Statistiques') . '" />
                </a>
            </p>';
        } else {
            $aff .= '<p class="text-center">
                <a href="top.php">
                    ' . translate('Top') . ' ' . $top . '
                </a>&nbsp;&nbsp;
                <a href="stats.php" >
                    ' . translate('Statistiques') . '
                </a>
            </p>';
        }

        global $block_title;

        $title = $block_title == '' ? translate('Activité du site') : $block_title;

        themesidebox($title, $aff);
    }
}
