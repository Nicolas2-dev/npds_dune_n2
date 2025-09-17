<?php

namespace App\Http\Controllers\Admin\;


use App\Http\Controllers\Core\AdminBaseController;


class extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        $f_meta_nom = 'blocks';

        // controle droit
        admindroits($aid, $f_meta_nom);

        global $language;
        $hlpfile = 'admin/manuels/' . $language . '/rightBlocks.html';

        parent::initialize();        
    }



function makerblock($title, $content, $members, $Mmember, $Rindex, $Scache, $BRaide, $SHTML, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(Sanitize::fixQuotes($title));
    $content = stripslashes(Sanitize::fixQuotes($content));

    if ($SHTML != 'ON') {
        $content = strip_tags(str_replace('<br />', "\n", $content));
    }

    sql_query("INSERT INTO " . sql_prefix('rblocks') . " 
               VALUES (NULL,'$title','$content', '$members', '$Rindex', '$Scache', '1', '$css', '$BRaide')");

    global $aid;
    Log::ecrireLog('security', sprintf('MakeRightBlock(%s) by AID : %s', Language::affLangue($title), $aid), '');

    Header('Location: admin.php?op=blocks');
}

function changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(Sanitize::fixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(Sanitize::fixQuotes($content));

    sql_query("UPDATE " . sql_prefix('rblocks') . " 
               SET title='$title', content='$content', member='$members', Rindex='$Rindex', cache='$Scache', actif='$Sactif', css='$css', aide='$BRaide' 
               WHERE id='$id'");

    global $aid;
    Log::ecrireLog('security', sprintf('ChangeRightBlock(%s - %s) by AID : %s', Language::affLangue($title), $id, $aid), '');

    Header('Location: admin.php?op=blocks');
}

function changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css)
{
    if (is_array($Mmember) and ($members == 1)) {
        $members = implode(',', $Mmember);

        if ($members == 0) {
            $members = 1;
        }
    }

    if (empty($Rindex)) {
        $Rindex = 0;
    }

    $title = stripslashes(Sanitize::fixQuotes($title));

    if ($Sactif == 'ON') {
        $Sactif = 1;
    } else {
        $Sactif = 0;
    }

    $content = stripslashes(Sanitize::fixQuotes($content));

    sql_query("INSERT INTO " . sql_prefix('lblocks') . " 
               VALUES (NULL,'$title','$content','$members', '$Rindex', '$Scache', '$Sactif', '$css', '$BRaide')");

    sql_query("DELETE FROM " . sql_prefix('rblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::ecrireLog('security', sprintf('MoveRightBlockToLeft(%s - %s) by AID : %s', Language::affLangue($title), $id, $aid), '');

    Header('Location: admin.php?op=blocks');
}

function deleterblock($id)
{
    sql_query("DELETE FROM " . sql_prefix('rblocks') . " 
               WHERE id='$id'");

    global $aid;
    Log::ecrireLog('security', sprintf('DeleteRightBlock(%s) by AID : %s', $id, $aid), '');

    Header('Location: admin.php?op=blocks');
}

settype($css, 'integer');
$Mmember = isset($Mmember) ? $Mmember : '';
settype($Sactif, 'string');
settype($SHTML, 'string');

switch ($op) {

    case 'makerblock':
        makerblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
        break;

    case 'deleterblock':
        deleterblock($id);
        break;

    case 'changerblock':
        changerblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;

    case 'gaucherblock':
        changegaucherblock($id, $title, $content, $members, $Mmember, $Rindex, $Scache, $Sactif, $BRaide, $css);
        break;
}
