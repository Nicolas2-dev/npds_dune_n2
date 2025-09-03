<?php

function push_header($operation)
{
    global $push_largeur, $push_largeur_suite, $push_titre, $push_logo;

    if ($operation == 'suite') {
        $push_largeur = $push_largeur_suite;
    }

    $temp  = "<table width=\"$push_largeur\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $temp .= "<tr><td width=\"100%\">";
    $temp .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
    $temp .= "<tr>";

    $push_titre = str_replace("'", "\'", $push_titre);

    $temp .= "<td width=\"100%\" align=\"center\"><span style=\"font-size: 11px;\"><b>" . htmlspecialchars($push_titre, ENT_COMPAT | ENT_HTML401, 'UTF-8') . "</b></td>";

    if ($push_logo != "") {
        $temp .= "</tr><tr><td width=\"100%\" background=\"$push_logo\">";
    } else {
        $temp .= "</tr><tr><td width=\"100%\">";
    }

    echo "<script type=\"text/javascript\">\n//<![CDATA[\ndocument.write('$temp');\n//]]>\n</script>";
}

function push_footer()
{
    $temp = "</td></tr></table></td></tr></table>";

    echo "document.write('$temp');\n";
}
