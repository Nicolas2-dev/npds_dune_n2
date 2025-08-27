<?php

namespace App\Library\Url;


class Url
{

    #autodoc redirect_url($urlx) : Permet une redirection javascript / en lieu et place de header('location: ...');
    function redirect_url($urlx)
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='" . $urlx . "';\n";
        echo "//]]>\n";
        echo "</script>";
    }
    
}
