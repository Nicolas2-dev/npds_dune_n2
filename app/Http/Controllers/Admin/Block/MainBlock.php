<?php

namespace App\Http\Controllers\Admin\Block;


use App\Http\Controllers\Core\AdminBaseController;


class MainBlock extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        $f_meta_nom = 'mblock';
        $f_titre = adm_translate('Bloc Principal');

        // controle droit
        admindroits($aid, $f_meta_nom);

        global $language;
        $hlpfile = 'admin/manuels/' . $language . '/mainblock.html';

        /*
        switch ($op) {

            case 'mblock':
                mblock();
                break;

            case 'changemblock':
                changemblock($title, $content);
                break;
        }

        case 'mblock':
        case 'changemblock':
            include 'admin/mainblock.php';
            break;

        */

        parent::initialize();        
    }

    public function mainBlock()
    {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        echo '
        <hr />
        <h3>' . adm_translate('Edition du Bloc Principal') . '</h3>';

        $result = sql_query("SELECT title, content 
                            FROM " . sql_prefix('block') . " 
                            WHERE id=1");

        if (sql_num_rows($result) > 0) {
            while (list($title, $content) = sql_fetch_row($result)) {
                echo '
                <form id="fad_mblock" action="admin.php" method="post">
                    <div class="form-floating mb-3">
                    <textarea class="form-control" type="text" id="title" name="title" maxlength="1000" placeholder="' . adm_translate('Titre :') . '" style="height:70px;">' . $title . '</textarea>
                    <label for="title">' . adm_translate('Titre') . '</label>
                    <span class="help-block text-end"><span id="countcar_title"></span></span>
                    </div>
                    <div class="form-floating mb-3">
                    <textarea class="form-control" id="content" name="content" style="height:170px;">' . $content . '</textarea>
                    <label for="content">' . adm_translate('Contenu') . '</label>
                    </div>
                    <input type="hidden" name="op" value="changemblock" />
                    <button class="btn btn-primary btn-block" type="submit">' . adm_translate('Valider') . '</button>
                </form>
                <script type="text/javascript">
                    //<![CDATA[
                        $(document).ready(function() {
                        inpandfieldlen("title",1000);
                        });
                    //]]>
                </script>';
            }
        }

        Validation::adminFoot('fv', '', '', '');
    }

    public function changeMainBlock($title, $content)
    {
        global $aid;

        $title = stripslashes(Sanitize::fixQuotes($title));
        $content = stripslashes(Sanitize::fixQuotes($content));

        sql_query("UPDATE " . sql_prefix('block') . " 
                SET title='$title', content='$content' WHERE id='1'");

        Log::ecrireLog('security', sprintf('ChangeMainBlock(%s) by AID : %s', Language::affLangue($title), $aid), '');

        Header('Location: admin.php?op=adminMain');
    }

}
