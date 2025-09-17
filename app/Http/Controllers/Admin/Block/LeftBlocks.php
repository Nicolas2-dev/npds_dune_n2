<?php

namespace App\Http\Controllers\Admin\Block;


use App\Http\Controllers\Core\AdminBaseController;


class LeftBlocks extends AdminBaseController
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
        $hlpfile = 'admin/manuels/' . $language . '/leftBlocks.html';

        /*
        settype($css, 'integer');
        $Mmember = isset($Mmember) ? $Mmember : '';
        settype($Sactif, 'string');
        settype($SHTML, 'string');

        switch ($op) {

            case 'makelblock':
                makelblock($title, $xtext, $members, $Mmember, $index, $Scache, $Baide, $SHTML, $css);
                break;

            case 'deletelblock':
                deletelblock($id);
                break;

            case 'changelblock':
                changelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
                break;

            case 'droitelblock':
                changedroitelblock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css);
                break;
        }

        case 'makelblock':
        case 'deletelblock':
        case 'changelblock':
        case 'droitelblock':
            include 'admin/leftBlocks.php';
            break;

        */

        parent::initialize();        
    }

    public function makeLeftBlock($title, $content, $members, $Mmember, $Lindex, $Scache, $BLaide, $SHTML, $css)
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) {
            $Lindex = 0;
        }

        $title = stripslashes(Sanitize::fixQuotes($title));
        $content = stripslashes(Sanitize::fixQuotes($content));

        if ($SHTML != 'ON') {
            $content = strip_tags(str_replace('<br />', '\n', $content));
        }

        sql_query("INSERT INTO " . sql_prefix('lblocks') . " 
                VALUES (NULL,'$title','$content','$members', '$Lindex', '$Scache', '1','$css', '$BLaide')");

        global $aid;
        Log::ecrireLog('security', "MakeLeftBlock(" . Language::affLangue($title) . ") by AID : $aid", "");

        Header('Location: admin.php?op=blocks');
    }

    public function changeLeftBlock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css)
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);

            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) {
            $Lindex = 0;
        }

        $title = stripslashes(Sanitize::fixQuotes($title));

        if ($Sactif == 'ON') {
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        if ($css) {
            $css = 1;
        } else {
            $css = 0;
        }

        $content = stripslashes(Sanitize::fixQuotes($content));
        $BLaide = stripslashes(Sanitize::fixQuotes($BLaide));

        sql_query("UPDATE " . sql_prefix('lblocks') . " 
                SET title='$title', content='$content', member='$members', Lindex='$Lindex', cache='$Scache', actif='$Sactif', aide='$BLaide', css='$css' 
                WHERE id='$id'");

        global $aid;
        Log::ecrireLog('security', "ChangeLeftBlock(" . Language::affLangue($title) . " - $id) by AID : $aid", '');

        Header('Location: admin.php?op=blocks');
    }

    public function changeDroiteLeftBlock($id, $title, $content, $members, $Mmember, $Lindex, $Scache, $Sactif, $BLaide, $css)
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);

            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) {
            $Lindex = 0;
        }

        $title = stripslashes(Sanitize::fixQuotes($title));
        if ($Sactif == 'ON') {
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        if ($css) {
            $css = 1;
        } else {
            $css = 0;
        }

        $content = stripslashes(Sanitize::fixQuotes($content));
        $BLaide = stripslashes(Sanitize::fixQuotes($BLaide));

        sql_query("INSERT INTO " . sql_prefix('rblocks') . " 
                VALUES (NULL,'$title','$content', '$members', '$Lindex', '$Scache', '$Sactif', '$css', '$BLaide')");

        sql_query("DELETE FROM " . sql_prefix('lblocks') . " 
                WHERE id='$id'");

        global $aid;
        Log::ecrireLog('security', "MoveLeftBlockToRight(" . Language::affLangue($title) . " - $id) by AID : $aid", '');

        Header('Location: admin.php?op=blocks');
    }

    public function deleteLeftBlock($id)
    {
        sql_query("DELETE FROM " . sql_prefix('lblocks') . " 
                WHERE id='$id'");

        global $aid;
        Log::ecrireLog('security', "DeleteLeftBlock($id) by AID : $aid", '');

        Header('Location: admin.php?op=blocks');
    }

}
