<?php

namespace App\Http\Controllers\Admin\Forum;


use App\Http\Controllers\Core\AdminBaseController;


class ForumConfig extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        $f_meta_nom = 'ForumConfigAdmin';
        $f_titre = adm_translate('Configuration des Forums');

        // controle droit
        admindroits($aid, $f_meta_nom);

        global $language, $adminimg, $admf_ext;
        $hlpfile = 'admin/manuels/' . $language . '/forumconfig.html';

        /*
        // FORUMS
        case 'ForumConfigAdmin':
            include 'admin/phpbbconfig.php';
            ForumConfigAdmin();
            break;

        case 'ForumConfigChange':
            include 'admin/phpbbconfig.php';
            ForumConfigChange($allow_html, $allow_bbcode, $allow_sig, $posts_per_page, $hot_threshold, $topics_per_page, $allow_upload_forum, $allow_forum_hide, $rank1, $rank2, $rank3, $rank4, $rank5, $antiFlood, $solved);
            break;
        */

        parent::initialize();        
    }

    public function ForumConfigAdmin()
    {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT * FROM " . sql_prefix('config'));

        list($allow_html, $allow_bbcode, $allow_sig, $posts_per_page, $hot_threshold, $topics_per_page, $allow_upload_forum, $allow_forum_hide, $forum_attachments, $rank1, $rank2, $rank3, $rank4, $rank5, $antiFlood, $solved) = sql_fetch_row($result);

        echo '
        <hr />
        <h3 class="mb-3">' . adm_translate('Configuration des Forums') . '</h3>
        <form id="phpbbconfigforum" action="admin.php" method="post">
            <div class="row">
                <label class="col-form-label col-sm-5" for="allow_html">' . adm_translate('Autoriser le HTML') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($allow_html == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_html_y" name="allow_html" value="1" ' . $cky . ' />
                    <label class="form-check-label" for="allow_html_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_html_n" name="allow_html" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="allow_html_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label col-sm-5 " for="allow_bbcode">' . adm_translate('Autoriser les Smilies') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($allow_bbcode == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_bbcode_y" name="allow_bbcode" value="1" ' . $cky . ' />
                    <label class="form-check-label" for="allow_bbcode_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_bbcode_n" name="allow_bbcode" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="allow_bbcode_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label col-sm-5" for="allow_sig">' . adm_translate('Autoriser les Signatures') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($allow_sig == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_sig_y" name="allow_sig" value="1" ' . $cky . ' />
                    <label class="form-check-label" for="allow_sig_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_sig_n" name="allow_sig" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="allow_sig_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-5" for="hot_threshold">' . adm_translate('Seuil pour les Sujet \'chauds\'') . '</label>
                <div class="col-sm-7">
                    <input class="form-control" type="text" min="0" id="hot_threshold" name="hot_threshold" maxlength="6" value="' . $hot_threshold . '" />
                    <span class="help-block text-end" id="countcar_hot_threshold"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-5" for="posts_per_page">' . adm_translate('Nombre de contributions par page') . '</label>
                <div class="col-sm-7">
                    <input class="form-control" type="text" min="0" id="posts_per_page" name="posts_per_page" maxlength="6" value="' . $posts_per_page . '" />
                    <span class="help-block">' . adm_translate('(C\'est le nombre de contributions affichées pour chaque page relative à un Sujet)') . '<span class="float-end ms-1" id="countcar_posts_per_page"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-5" for="topics_per_page">' . adm_translate('Sujets par forum') . '</label>
                <div class="col-sm-7">
                    <input class="form-control" type="text" min="0" id="topics_per_page" name="topics_per_page" maxlength="6" value="' . $topics_per_page . '" />
                    <span class="help-block">' . adm_translate('(C\'est le nombre de Sujets affichés pour chaque page relative à un Forum)') . '<span class="float-end ms-1" id="countcar_topics_per_page"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-5" for="antiFlood">' . adm_translate('Nombre maximum de contributions par IP et par période de 30 minutes (0=système inactif)') . '</label>
                <div class="col-sm-7">
                    <input class="form-control" type="text" min="0" id="antiFlood" name="antiFlood" maxlength="6" value="' . $antiFlood . '" />
                    <span class="help-block text-end" id="countcar_antiFlood"></span>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label col-sm-5" for="solved">' . adm_translate('Activer le tri des contributions \'résolues\'') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($solved == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="solved_y" name="solved" value="1" ' . $cky . ' />
                    <label class="form-check-label" for="solved_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="solved_n" name="solved" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="solved_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label col-sm-5" for="allow_upload_forum">' . adm_translate('Activer l\'upload dans les forums ?') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($allow_upload_forum == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_upload_forum_y" name="allow_upload_forum" value="1" ' . $cky . ' />
                    <label class="form-check-label" for="allow_upload_forum_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_upload_forum_n" name="allow_upload_forum" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="allow_upload_forum_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label col-sm-5" for="allow_forum_hide">' . adm_translate('Activer les textes cachés') . '</label>
                <div class="col-sm-7 my-2">';

        $cky = '';
        $ckn = '';

        if ($allow_forum_hide == 1) {
            $cky = 'checked="checked"';
            $ckn = '';
        } else {
            $cky = '';
            $ckn = 'checked="checked"';
        }

        echo '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_forum_hide_y" name="allow_forum_hide" value="1" ' . $cky . '/>
                    <label class="form-check-label" for="allow_forum_hide_y">' . adm_translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="allow_forum_hide_n" name="allow_forum_hide" value="0" ' . $ckn . ' />
                    <label class="form-check-label" for="allow_forum_hide_n">' . adm_translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 form-floating">
                <textarea class="form-control" id="rank1" name="rank1" rows="3" maxlength="255">' . $rank1 . '</textarea>
                <label for="rank1">' . adm_translate('Texte pour le rôle') . ' 1 </label>
                <span class="help-block text-end" id="countcar_rank1"></span>
            </div>
            <div class="mb-3 form-floating">
                <textarea class="form-control" id="rank2" name="rank2" rows="3" maxlength="255">' . $rank2 . '</textarea>
                <label for="rank2">' . adm_translate('Texte pour le rôle') . ' 2 </label>
                <span class="help-block text-end" id="countcar_rank2"></span>
            </div>
            <div class="mb-3 form-floating">
                <textarea class="form-control" id="rank3" name="rank3" rows="3" maxlength="255">' . $rank3 . '</textarea>
                <label for="rank3">' . adm_translate('Texte pour le rôle') . ' 3 </label>
                <span class="help-block text-end" id="countcar_rank3"></span>
            </div>
            <div class="mb-3 form-floating">
                <textarea class="form-control" id="rank4" name="rank4" rows="3" maxlength="255">' . $rank4 . '</textarea>
                <label for="rank4">' . adm_translate('Texte pour le rôle') . ' 4 </label>
                <span class="help-block text-end" id="countcar_rank4"></span>
            </div>
            <div class="mb-3 form-floating">
                <textarea class="form-control" id="rank5" name="rank5" rows="3" maxlength="255">' . $rank5 . '</textarea>
                <label for="rank5">' . adm_translate('Texte pour le rôle') . ' 5 </label>
                <span class="help-block text-end" id="countcar_rank5"></span>
            </div>
            <input type="hidden" name="op" value="ForumConfigChange" />
            <div class="mb-3">
                <button class="btn btn-primary" type="submit">' . adm_translate('Changer') . '</button>
            </div>
        </form>';

        $fv_parametres = '
            hot_threshold: {
                validators: {
                    regexp: {
                    regexp:/^\d{1,6}$/,
                    message: "0-9"
                    }
                }
            },
            posts_per_page: {
                validators: {
                    regexp: {
                    regexp:/^\d{1,6}$/,
                    message: "0-9"
                    }
                }
            },
            topics_per_page: {
                validators: {
                    regexp: {
                    regexp:/^\d{1,6}$/,
                    message: "0-9"
                    }
                }
            },
            antiFlood: {
                validators: {
                    regexp: {
                    regexp:/^\d{1,6}$/,
                    message: "0-9"
                    }
                }
            },';

        $arg1 = 'var formulid = ["phpbbconfigforum"];
            inpandfieldlen("posts_per_page",255);
            inpandfieldlen("hot_threshold",255);
            inpandfieldlen("topics_per_page",255);
            inpandfieldlen("antiFlood",255);
            inpandfieldlen("rank1",255);
            inpandfieldlen("rank2",255);
            inpandfieldlen("rank3",255);
            inpandfieldlen("rank4",255);
            inpandfieldlen("rank5",255);';

        Validation::adminFoot('fv', $fv_parametres, $arg1, '');
    }

    public function ForumConfigChange($allow_html, $allow_bbcode, $allow_sig, $posts_per_page, $hot_threshold, $topics_per_page, $allow_upload_forum, $allow_forum_hide, $rank1, $rank2, $rank3, $rank4, $rank5, $antiFlood, $solved)
    {
        sql_query("UPDATE " . sql_prefix('config') . " 
                SET allow_html='$allow_html', allow_bbcode='$allow_bbcode', allow_sig='$allow_sig', posts_per_page='$posts_per_page', hot_threshold='$hot_threshold', topics_per_page='$topics_per_page', allow_upload_forum='$allow_upload_forum', allow_forum_hide='$allow_forum_hide', rank1='$rank1', rank2='$rank2', rank3='$rank3', rank4='$rank4', rank5='$rank5', antiFlood='$antiFlood', solved='$solved'");

        q_clean();

        Header('Location: admin.php?op=ForumConfigAdmin');
    }
    
}
