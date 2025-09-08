<?php

use App\Library\Assets\Js;
use App\Library\Block\Block;
use App\Library\Theme\Theme;
use App\Library\Media\Smilies;
use App\Library\Encryption\Encrypter;

if (! function_exists('makeChatBox')) {
    #autodoc makeChatBox($pour) : Bloc ChatBox <br />=> syntaxe : function#makeChatBox <br />params#chat_membres <br /> le parametre doit être en accord avec l'autorisation donc (chat_membres, chat_tous, chat_admin, chat_anonyme)
    function makeChatBox($pour)
    {
        global $user, $admin, $member_list, $long_chain;

        include_once 'functions.php';

        $auto = Block::autorisationBlock('params#' . $pour);
        $dimauto = count($auto);

        if (!$long_chain) {
            $long_chain = 12;
        }

        $thing = '';
        $une_ligne = false;

        if ($dimauto <= 1) {
            $counter = sql_num_rows(sql_query("SELECT message 
                                            FROM " . sql_prefix('chatbox') . " 
                                            WHERE id='" . $auto[0] . "'")) - 6;

            if ($counter < 0) {
                $counter = 0;
            }

            $result = sql_query("SELECT username, message, dbname 
                                FROM " . sql_prefix('chatbox') . " 
                                WHERE id='" . $auto[0] . "' 
                                ORDER BY date ASC 
                                LIMIT $counter, 6");

            if ($result) {
                while (list($username, $message, $dbname) = sql_fetch_row($result)) {
                    if (isset($username)) {
                        if ($dbname == 1) {
                            $thing .= ((!$user) and ($member_list == 1) and (!$admin))
                                ? '<span class="">' . substr($username, 0, 8) . '.</span>'
                                : "<a href=\"user.php?op=userinfo&amp;uname=$username\">" . substr($username, 0, 8) . ".</a>";
                        } else {
                            $thing .= '<span class="">' . substr($username, 0, 8) . '.</span>';
                        }
                    }

                    $une_ligne = true;

                    $thing .= (strlen($message) > $long_chain)
                        ? "&gt;&nbsp;<span>" . Smilies::smilie(stripslashes(substr($message, 0, $long_chain))) . " </span><br />\n"
                        : "&gt;&nbsp;<span>" . Smilies::smilie(stripslashes($message)) . " </span><br />\n";
                }
            }

            $PopUp = Js::javaPopup("chat.php?id=" . $auto[0] . "&amp;auto=" . Encrypter::encrypt(serialize($auto[0])), "chat" . $auto[0], 380, 480);

            if ($une_ligne) {
                $thing .= '<hr />';
            }

            $result = sql_query("SELECT DISTINCT ip 
                                FROM " . sql_prefix('chatbox') . " 
                                WHERE id='" . $auto[0] . "' 
                                AND date >= " . (time() - (60 * 2)) . "");

            $numofchatters = sql_num_rows($result);

            $thing .= $numofchatters > 0
                ? '<div class="d-flex"><a id="' . $pour . '_encours" class="fs-4" href="javascript:void(0);" onclick="window.open(' . $PopUp . ');" title="' . translate('Cliquez ici pour entrer') . ' ' . $pour . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-comments fa-2x nav-link faa-pulse animated faa-slow"></i></a><span class="badge rounded-pill bg-primary ms-auto align-self-center" title="' . translate('personne connectée.') . '" data-bs-toggle="tooltip">' . $numofchatters . '</span></div>'
                : '<div><a id="' . $pour . '" href="javascript:void(0);" onclick="window.open(' . $PopUp . ');" title="' . translate('Cliquez ici pour entrer') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-comments fa-2x "></i></a></div>';
        } else {
            if (count($auto) > 1) {

                $numofchatters = 0;
                $thing .= '<ul>';

                foreach ($auto as $autovalue) {
                    $result = Q_select("SELECT groupe_id, groupe_name 
                                        FROM " . sql_prefix('groupes') . " 
                                        WHERE groupe_id='$autovalue'", 3600);

                    $autovalueX = $result[0];

                    $PopUp = Js::javaPopup('chat.php?id=' . $autovalueX['groupe_id'] . '&auto=' . Encrypter::encrypt(serialize($autovalueX['groupe_id'])), 'chat' . $autovalueX['groupe_id'], 380, 480);

                    $thing .= "<li><a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">" . $autovalueX['groupe_name'] . "</a>";

                    $result = sql_query("SELECT DISTINCT ip 
                                        FROM " . sql_prefix('chatbox') . " 
                                        WHERE id='" . $autovalueX['groupe_id'] . "' 
                                        AND date >= " . (time() - (60 * 3)) . "");

                    $numofchatters = sql_num_rows($result);

                    if ($numofchatters) {
                        $thing .= '&nbsp;(<span class="text-danger"><b>' . sql_num_rows($result) . '</b></span>)';
                    }

                    echo '</li>';
                }

                $thing .= '</ul>';
            }
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = translate('Bloc Chat');
        }

        Theme::themeSidebox($block_title, $thing);

        sql_free_result($result);
    }
}
