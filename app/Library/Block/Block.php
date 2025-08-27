<?php

namespace App\Library\Block;


class Block
{

    function load_blocks(string $dir): void
    {
        if (!is_dir($dir)) {
            trigger_error('Dossier introuvable : '. $dir, E_USER_WARNING);
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
    }

    #autodoc block_fonction($title, $contentX) : Assure la gestion des include# et function# des blocs de NPDS / le titre du bloc est exporté (global) )dans $block_title
    function block_fonction($title, $contentX)
    {
        global $block_title;

        $block_title = $title;

        //For including PHP functions in block
        if (stristr($contentX, 'function#')) {

            $contentX = str_replace('<br />', '', $contentX);
            $contentX = str_replace('<BR />', '', $contentX);
            $contentX = str_replace('<BR>', '', $contentX);
            $contentY = trim(substr($contentX, 9));

            if (stristr($contentY, 'params#')) {
                $pos        = strpos($contentY, 'params#');
                $contentII  = trim(substr($contentY, 0, $pos));

                $params     = substr($contentY, $pos + 7);
                $prm        = explode(',', $params);

                // Remplace le param 'False' par la valeur false (idem pour True)
                for ($i = 0; $i <= count($prm) - 1; $i++) {
                    if ($prm[$i] == 'false') {
                        $prm[$i] = false;
                    }

                    if ($prm[$i] == 'true') {
                        $prm[$i] = true;
                    }
                }

                // En fonction du nombre de params de la fonction : limite actuelle : 8
                if (function_exists($contentII)) {
                    switch (count($prm)) {

                        case 1:
                            $contentII($prm[0]);
                            break;

                        case 2:
                            $contentII($prm[0], $prm[1]);
                            break;

                        case 3:
                            $contentII($prm[0], $prm[1], $prm[2]);
                            break;

                        case 4:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3]);
                            break;

                        case 5:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4]);
                            break;

                        case 6:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5]);
                            break;

                        case 7:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5], $prm[6]);
                            break;

                        case 8:
                            $contentII($prm[0], $prm[1], $prm[2], $prm[3], $prm[4], $prm[5], $prm[6], $prm[7]);
                            break;
                    }

                    return true;
                } else {
                    return false;
                }
            } else {
                if (function_exists($contentY)) {
                    $contentY();

                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    #autodoc fab_block($title, $member, $content, $Xcache) : Assure la fabrication réelle et le Cache d'un bloc
    function fab_block($title, $member, $content, $Xcache)
    {
        global $SuperCache, $CACHE_TIMINGS;

        // Multi-Langue
        $title = aff_langue($title);

        // Bloc caché
        $hidden = false;

        if (substr($content, 0, 7) == 'hidden#') {
            $content = str_replace('hidden#', '', $content);
            $hidden = true;
        }

        // Si on cherche à charger un JS qui a déjà été chargé par pages.php alors on ne le charge pas ...
        global $pages_js;
        if ($pages_js != '') {
            preg_match('#src="([^"]*)#', $content, $jssrc);

            if (is_array($pages_js)) {
                foreach ($pages_js as $jsvalue) {
                    if (array_key_exists('1', $jssrc)) {
                        if ($jsvalue == $jssrc[1]) {
                            $content = '';
                            break;
                        }
                    }
                }
            } else {
                if (array_key_exists('1', $jssrc)) {
                    if ($pages_js == $jssrc[1]) {
                        $content = '';
                    }
                }
            }
        }

        $content = aff_langue($content);

        if (($SuperCache) and ($Xcache != 0)) {
            $cache_clef = md5($content);
            $CACHE_TIMINGS[$cache_clef] = $Xcache;

            $cache_obj = new SuperCacheManager();
            $cache_obj->startCachingBlock($cache_clef);
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($Xcache == 0)) {

            global $user, $admin;

            // For including CLASS AND URI in Block
            global $B_class_title, $B_class_content;

            $B_class_title = '';
            $B_class_content = '';
            $R_uri = '';

            if (stristr($content, 'class-') or stristr($content, 'uri')) {
                $tmp = explode("\n", $content);
                $content = '';

                foreach ($tmp as $id => $class) {
                    $temp = explode('#', $class);

                    if ($temp[0] == 'class-title') {
                        $B_class_title = str_replace("\r", '', $temp[1]);

                    } else if ($temp[0] == 'class-content') {
                        $B_class_content = str_replace("\r", '', $temp[1]);

                    } else if ($temp[0] == 'uri') {
                        $R_uri = str_replace("\r", '', $temp[1]);

                    } else {
                        if ($content != '') {
                            $content .= "\n ";
                        }

                        $content .= str_replace("\r", '', $class);
                    }
                }
            }

            // For BLOC URIs
            if ($R_uri) {
                global $REQUEST_URI;

                $page_ref = basename($REQUEST_URI);
                $tab_uri = explode(' ', $R_uri);

                $R_content = false;
                $tab_pref = parse_url($page_ref);
                $racine_page = $tab_pref['path'];

                if (array_key_exists('query', $tab_pref)) {
                    $tab_pref = explode('&', $tab_pref['query']);
                }

                foreach ($tab_uri as $RR_uri) {
                    $tab_puri = parse_url($RR_uri);
                    $racine_uri = $tab_puri['path'];

                    if ($racine_page == $racine_uri) {
                        if (array_key_exists('query', $tab_puri)) {
                            $tab_puri = explode('&', $tab_puri['query']);
                        }

                        foreach ($tab_puri as $idx => $RRR_uri) {
                            if (substr($RRR_uri, -1) == '*') {
                                // si le token contient *
                                if (substr($RRR_uri, 0, strpos($RRR_uri, '=')) == substr($tab_pref[$idx], 0, strpos($tab_pref[$idx], '='))) {
                                    $R_content = true;
                                }
                            } else {
                                $R_content = array_key_exists($RRR_uri, $tab_pref) ? false : true;
                            }
                        }
                    }

                    if ($R_content == true) {
                        break;
                    }
                }

                if (!$R_content) {
                    $content = '';
                }
            }

            // For Javascript in Block
            if (!stristr($content, 'javascript')) {
                $content = nl2br($content);
            }

            // For including externale file in block / the return MUST BE in $content
            if (stristr($content, 'include#')) {
                $Xcontent = false;

                // You can now, include AND cast a fonction with params in the same bloc !
                if (stristr($content, 'function#')) {
                    $content = str_replace('<br />', '', $content);
                    $content = str_replace('<BR />', '', $content);
                    $content = str_replace('<BR>', '', $content);

                    $pos = strpos($content, 'function#');

                    $Xcontent = substr(trim($content), $pos);
                    $content = substr(trim($content), 8, $pos - 10);
                } else {
                    $content = substr(trim($content), 8);
                }

                include_once $content;

                if ($Xcontent) {
                    $content = $Xcontent;
                }
            }

            if (!empty($content)) {
                if (($member == 1) and (isset($user))) {
                    if (!block_fonction($title, $content)) {
                        if (!$hidden) {
                            themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif ($member == 0) {
                    if (!block_fonction($title, $content)) {
                        if (!$hidden) {
                            themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif (($member > 1) and (isset($user))) {
                    $tab_groupe = valid_group($user);

                    if (groupe_autorisation($member, $tab_groupe)) {
                        if (!block_fonction($title, $content)) {
                            if (!$hidden) {
                                themesidebox($title, $content);
                            } else {
                                echo $content;
                            }
                        }
                    }
                } elseif (($member == -1) and (!isset($user))) {
                    if (!block_fonction($title, $content)) {
                        if (!$hidden) {
                            themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                } elseif (($member == -127) and (isset($admin)) and ($admin)) {
                    if (!block_fonction($title, $content)) {
                        if (!$hidden) {
                            themesidebox($title, $content);
                        } else {
                            echo $content;
                        }
                    }
                }
            }

            if (($SuperCache) and ($Xcache != 0)) {
                $cache_obj->endCachingBlock($cache_clef);
            }
        }
    }

    #autodoc leftblocks() : Meta-Fonction / Blocs de Gauche
    function leftblocks($moreclass)
    {
        Pre_fab_block('', 'LB', $moreclass);
    }

    #autodoc rightblocks() : Meta-Fonction / Blocs de Droite
    function rightblocks($moreclass)
    {
        Pre_fab_block('', 'RB', $moreclass);
    }

    #autodoc oneblock($Xid, $Xblock) : Alias de Pre_fab_block pour meta-lang
    function oneblock($Xid, $Xblock)
    {
        ob_start();
            Pre_fab_block($Xid, $Xblock, '');
            $tmp = ob_get_contents();
        ob_end_clean();

        return $tmp;
    }

    #autodoc Pre_fab_block($Xid, $Xblock, $moreclass) : Assure la fabrication d'un ou de tous les blocs Gauche et Droite
    function Pre_fab_block($Xid, $Xblock, $moreclass)
    {
        global $htvar; // modif Jireck

        if ($Xid) {
            $result = $Xblock == 'RB' 
                ? sql_query("SELECT title, content, member, cache, actif, id, css 
                            FROM " . sql_prefix('rblocks') . " 
                            WHERE id='$Xid'") 

                : sql_query("SELECT title, content, member, cache, actif, id, css 
                            FROM " . sql_prefix('lblocks') . " 
                            WHERE id='$Xid'");
        } else {
            $result = $Xblock == 'RB' 
                ? sql_query("SELECT title, content, member, cache, actif, id, css 
                            FROM " . sql_prefix('rblocks') . " 
                            ORDER BY Rindex ASC") 

                : sql_query("SELECT title, content, member, cache, actif, id, css 
                            FROM " . sql_prefix('lblocks') . " 
                            ORDER BY Lindex ASC");
        }

        global $bloc_side;
        $bloc_side = $Xblock == 'RB' ? 'RIGHT' : 'LEFT';

        while (list($title, $content, $member, $cache, $actif, $id, $css) = sql_fetch_row($result)) {
            if (($actif) or ($Xid)) {

                $htvar = ($css == 1) 
                    ? '<div class="' . $moreclass . '" id="' . $Xblock . '_' . $id . '">' 
                    : '<div class="' . $moreclass . ' ' . strtolower($bloc_side) . 'bloc">';

                fab_block($title, $member, $content, $cache);
                // echo "</div>"; // modif Jireck
            }
        }

        sql_free_result($result);
    }

    #autodoc niv_block($Xcontent) : Retourne le niveau d'autorisation d'un block (et donc de certaines fonctions) / le paramètre (une expression régulière) est le contenu du bloc (function#....)
    function niv_block($Xcontent)
    {
        $result = sql_query("SELECT member, actif 
                            FROM " . sql_prefix('rblocks') . " 
                            WHERE content REGEXP '$Xcontent'");

        if (sql_num_rows($result)) {
            list($member, $actif) = sql_fetch_row($result);

            return ($member . ',' . $actif);
        }

        $result = sql_query("SELECT member, actif 
                            FROM " . sql_prefix('lblocks') . " 
                            WHERE content REGEXP '$Xcontent'");

        if (sql_num_rows($result)) {
            list($member, $actif) = sql_fetch_row($result);

            return ($member . ',' . $actif);
        }

        sql_free_result($result);
    }

    #autodoc autorisation_block($Xcontent) : Retourne une chaine?? // array ou vide contenant la liste des autorisations (-127,-1,0,1,2...126)) SI le bloc est actif SINON "" / le paramètre est le contenu du bloc (function#....)
    function autorisation_block($Xcontent)
    {
        $autoX = array(); //notice .... to follow

        $auto = explode(',', niv_block($Xcontent));

        // le dernier indice indique si le bloc est actif
        $actif = $auto[count($auto) - 1];

        // on dépile le dernier indice
        array_pop($auto);

        foreach ($auto as $autovalue) {
            if (autorisation($autovalue)) {
                $autoX[] = $autovalue;
            }
        }

        if ($actif) {
            return $autoX;
        } else {
            return '';
        }
    }

}
