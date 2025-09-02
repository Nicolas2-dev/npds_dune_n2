<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

function winUpload(string $typeL): string|null
{
    $url = 'modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=minisite-ges';

    $toolbarOptions = [
        'menubar'    => 'no',
        'location'   => 'no',
        'directories' => 'no',
        'status'     => 'no',
        'copyhistory' => 'no',
        'toolbar'    => 'no',
        'scrollbars' => 'yes',
        'resizable'  => 'yes',
        'width'      => 780,
        'height'     => 500,
    ];

    // transforme le tableau en string JS
    $toolbarJs = implode(', ', array_map(
        fn($k, $v) => "$k=$v",
        array_keys($toolbarOptions),
        $toolbarOptions
    ));

    if ($typeL === 'win') {
        echo <<<HTML
        <script type="text/javascript">
        //<![CDATA[
            window.open('$url', 'wtmpMinisite', '$toolbarJs');
        //]]>
        </script>
        HTML;
        return null;
    }

    // si ce n’est pas pour ouverture immédiate, on renvoie juste la chaîne JS
    return "'$url', 'wtmpMinisite', '$toolbarJs'";
}
