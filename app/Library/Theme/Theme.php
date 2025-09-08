<?php

namespace App\Library\Theme;

use IntlDateFormatter;
use App\Library\Date\Date;
use App\Library\User\User;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;


class Theme
{

    /**
     * Retourne le chemin complet de l'image si elle existe dans le répertoire du thème.
     *
     * @param string $theme_img Nom du fichier image
     * @return string|false Chemin complet si trouvé, sinon false
     */
    public static function image(string $theme_img): string|false
    {
        global $theme;

        if (@file_exists('themes/' . $theme . '/assets/' . $theme_img)) {
            return 'themes/' . $theme . '/assets/' . $theme_img;
        }

        return false;
    }

    /**
     * Alias de self::image() pour retrouver une image dans le thème actif.
     *
     * @param string $theme_img Nom du fichier image
     * @return string|false Chemin complet si trouvé, sinon false
     */
    public static function themeImage($theme_img)
    {
        return static::image($theme_img);
    }

    /**
     * Retourne la liste des thèmes disponibles dans le dossier 'themes'.
     *
     * Les dossiers commençant par "_" ou contenant "base" ou un "." sont ignorés.
     *
     * @return string Liste des thèmes séparés par un espace
     */
    public static function themeList(): string
    {
        $themelist = [];
        $handle = opendir('themes');

        if ($handle !== false) {
            while (false !== ($file = readdir($handle))) {
                if (($file[0] !== '_')
                    && (!strstr($file, '.'))
                    && (!strstr($file, 'base'))
                ) {
                    $themelist[] = $file;
                }
            }

            natcasesort($themelist);
            closedir($handle);
        }

        return implode(' ', $themelist);
    }

    /**
     * Extrait une variable locale marquée par !var! dans le texte.
     *
     * @param string $Xcontent Contenu texte contenant éventuellement !var!VariableName
     * @return string|null Retourne le nom de la variable si trouvé, sinon null
     */
    public static function localVar(string $Xcontent): ?string
    {
        if (strstr($Xcontent, '!var!')) {
            $deb = strpos($Xcontent, '!var!', 0) + 5;
            $fin = strpos($Xcontent, ' ', $deb);

            if ($fin) {
                $H_var = substr($Xcontent, $deb, $fin - $deb);
            } else {
                $H_var = substr($Xcontent, $deb);
            }

            return $H_var;
        }

        return null;
    }

    /**
     * Génère le contenu d'une news pour l'index.
     *
     * @param string $aid ID de l'auteur
     * @param string $informant Nom de l'émetteur
     * @param int $time Timestamp
     * @param string $title Titre de l'article
     * @param int $counter Nombre de lectures
     * @param string|int $topic ID du topic
     * @param string $thetext Contenu de l'article
     * @param string $notes Notes associées
     * @param array $morelink Liens supplémentaires (read more, comments, etc.)
     * @param string $topicname Nom du topic
     * @param string $topicimage Image du topic
     * @param string $topictext Description du topic
     * @param string|int $id ID de l'article
     * @return void
     */
    public static function themeIndex(
        string      $aid,
        string      $informant,
        int         $time,
        string      $title,
        int         $counter,
        string|int  $topic,
        string      $thetext,
        string      $notes,
        array       $morelink,
        string      $topicname,
        string      $topicimage,
        string      $topictext,
        string|int  $id
    ): void {
        global $tipath, $theme;

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/views/partials/news/index-news.php')) {
            $inclusion = 'themes/' . $theme . '/views/partials/news/index-news.php';
        } elseif (file_exists('themes/base/views/partials/news/index-news.php')) {
            $inclusion = 'themes/base/views/partials/news/index-news.php';
        } else {
            echo 'index-news.php manquant / not find !<br />';
            die();
        }

        $H_var = static::localVar($thetext);

        if ($H_var != '') {
            ${$H_var} = true;

            $thetext = str_replace("!var!$H_var", "", $thetext);
        }

        if ($notes != '') {
            $notes = '<div class="note">' . translate("Note") . ' : ' . $notes . '</div>';
        }

        ob_start();
        include $inclusion;
        $Xcontent = ob_get_contents();
        ob_end_clean();

        $lire_la_suite = '';

        if ($morelink[0]) {
            $lire_la_suite = $morelink[1] . ' ' . $morelink[0] . ' | ';
        }

        $commentaire = '';

        if ($morelink[2]) {
            $commentaire = $morelink[2] . ' ' . $morelink[3] . ' | ';
        } else {
            $commentaire = $morelink[3] . ' | ';
        }

        $categorie = '';

        if ($morelink[6]) {
            $categorie = ' : ' . $morelink[6];
        }

        $morel = $lire_la_suite . $commentaire . $morelink[4] . ' ' . $morelink[5] . $categorie;

        $Xsujet = '';

        if ($topicimage != '') {
            if (!$imgtmp = Theme::themeImage('topics/' . $topicimage)) {
                $imgtmp = $tipath . $topicimage;
            }

            $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $imgtmp . '" alt="' . translate("Rechercher dans") . ' : ' . $topicname . '" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true" /></a>';
        } else {
            $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><span class="badge bg-secondary h1" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true">' . $topicname . '</span></a>';
        }

        $npds_METALANG_words = array(
            "'!N_publicateur!'i"    => $aid,
            "'!N_emetteur!'i"       => User::userPopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '">' . $informant . '</a>',
            "'!N_date!'i"           => Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT),
            "'!N_date_y!'i"         => Date::getPartOfTime($time, 'yyyy'),
            "'!N_date_m!'i"         => Date::getPartOfTime($time, 'MMMM'),
            "'!N_date_d!'i"         => Date::getPartOfTime($time, 'd'),
            "'!N_date_h!'i"         => Date::formatTimes($time, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM),
            "'!N_print!'i"          => $morelink[4],
            "'!N_friend!'i"         => $morelink[5],
            "'!N_nb_carac!'i"       => $morelink[0],
            "'!N_read_more!'i"      => $morelink[1],
            "'!N_nb_comment!'i"     => $morelink[2],
            "'!N_link_comment!'i"   => $morelink[3],
            "'!N_categorie!'i"      => $morelink[6],
            "'!N_titre!'i"          => $title,
            "'!N_texte!'i"          => $thetext,
            "'!N_id!'i"             => $id,
            "'!N_sujet!'i"          => $Xsujet,
            "'!N_note!'i"           => $notes,
            "'!N_nb_lecture!'i"     => $counter,
            "'!N_suite!'i"          => $morel
        );

        echo Metalang::metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
    }

    /**
     * Génère le contenu détaillé d'un article.
     *
     * @param string $aid ID de l'auteur
     * @param string $informant Nom de l'émetteur
     * @param int $time Timestamp
     * @param string $title Titre de l'article
     * @param string $thetext Contenu de l'article
     * @param string|int $topic ID du topic
     * @param string $topicname Nom du topic
     * @param string $topicimage Image du topic
     * @param string $topictext Description du topic
     * @param string|int $id ID de l'article
     * @param int|null $previous_sid ID de l'article précédent
     * @param int|null $next_sid ID de l'article suivant
     * @param string|null $archive Archive associée
     * @return void
     */
    public static function themeArticle(
        string $aid,
        string      $informant,
        int         $time,
        string      $title,
        string      $thetext,
        string|int  $topic,
        string      $topicname,
        string      $topicimage,
        string      $topictext,
        string|int  $id,
        ?int        $previous_sid,
        ?int        $next_sid,
        ?string     $archive
    ): void {
        global $tipath, $theme, $counter, $boxtitle, $boxstuff;

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/views/partials/news/detail-news.php")) {
            $inclusion = "themes/" . $theme . "/views/partials/news/detail-news.php";
        } elseif (file_exists("themes/base/views/partials/news/detail-news.php")) {
            $inclusion = "themes/base/views/partials/news/detail-news.php";
        } else {
            echo 'detail-news.php manquant / not find !<br />';
            die();
        }

        $H_var = static::localVar($thetext);

        if ($H_var != '') {
            ${$H_var} = true;

            $thetext = str_replace("!var!$H_var", '', $thetext);
        }

        ob_start();
        include $inclusion;
        $Xcontent = ob_get_contents();
        ob_end_clean();

        if ($previous_sid) {
            $prevArt = '<a href="article.php?sid=' . $previous_sid . '&amp;archive=' . $archive . '" ><i class="fa fa-chevron-left fa-lg me-2" title="' . translate("Précédent") . '" data-bs-toggle="tooltip"></i><span class="d-none d-sm-inline">' . translate("Précédent") . '</span></a>';
        } else {
            $prevArt = '';
        }

        if ($next_sid) {
            $nextArt = '<a href="article.php?sid=' . $next_sid . '&amp;archive=' . $archive . '" ><span class="d-none d-sm-inline">' . translate("Suivant") . '</span><i class="fa fa-chevron-right fa-lg ms-2" title="' . translate("Suivant") . '" data-bs-toggle="tooltip"></i></a>';
        } else {
            $nextArt = '';
        }

        $printP = '<a href="print.php?sid=' . $id . '" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-print"></i></a>';
        $sendF = '<a href="friend.php?op=FriendSend&amp;sid=' . $id . '" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-at"></i></a>';

        if (!$imgtmp = Theme::themeImage('topics/' . $topicimage)) {
            $imgtmp = $tipath . $topicimage;
        }

        $timage = $imgtmp;

        $npds_METALANG_words = array(
            "'!N_publicateur!'i"        => $aid,
            "'!N_emetteur!'i"           => User::userPopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '"><span class="">' . $informant . '</span></a>',
            "'!N_date!'i"               => Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT),
            "'!N_date_y!'i"             => Date::getPartOfTime($time, 'yyyy'),
            "'!N_date_m!'i"             => Date::getPartOfTime($time, 'MMMM'),
            "'!N_date_d!'i"             => Date::getPartOfTime($time, 'd'),
            "'!N_date_h!'i"             => Date::formatTimes($time, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM),
            "'!N_print!'i"              => $printP,
            "'!N_friend!'i"             => $sendF,
            "'!N_boxrel_title!'i"       => $boxtitle,
            "'!N_boxrel_stuff!'i"       => $boxstuff,
            "'!N_titre!'i"              => $title,
            "'!N_id!'i"                 => $id,
            "'!N_previous_article!'i"   => $prevArt,
            "'!N_next_article!'i"       => $nextArt,
            "'!N_sujet!'i"              => '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $timage . '" alt="' . translate("Rechercher dans") . '&nbsp;' . $topictext . '" /></a>',
            "'!N_texte!'i"              => $thetext,
            "'!N_nb_lecture!'i"         => $counter
        );

        echo Metalang::metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
    }

    /**
     * Génère un bloc latéral pour le thème.
     *
     * @param string $title Titre du bloc
     * @param string $content Contenu HTML du bloc
     * @return void
     */
    public static function themeSidebox(string $title, string $content): void
    {
        global $theme, $B_class_title, $B_class_content, $bloc_side, $htvar;

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/views/partials/block/bloc-right.php') && ($bloc_side == 'RIGHT')) {
            $inclusion = 'themes/' . $theme . '/views/partials/block/bloc-right.php';
        }

        if (file_exists('themes/' . $theme . '/views/partials/block/bloc-left.php') && ($bloc_side == 'LEFT')) {
            $inclusion = 'themes/' . $theme . '/views/partials/block/bloc-left.php';
        }

        if (!$inclusion) {
            if (file_exists('themes/' . $theme . '/views/partials/block/bloc.php')) {
                $inclusion = 'themes/' . $theme . '/views/partials/block/bloc.php';
            } elseif (file_exists('themes/base/views/partials/block/bloc.php')) {
                $inclusion = 'themes/base/views/partials/block/bloc.php';
            } else {
                echo 'bloc.php manquant / not find !<br />';
                die();
            }
        }

        ob_start();
        include $inclusion;
        $Xcontent = ob_get_contents();
        ob_end_clean();

        if ($title == 'no-title') {
            $Xcontent = str_replace('<div class="LB_title">!B_title!</div>', '', $Xcontent);
            $title = '';
        }

        $npds_METALANG_words = array(
            "'!B_title!'i"          => $title,
            "'!B_class_title!'i"    => isset($B_class_title) && $B_class_title !== '' ? $B_class_title : 'noclass',
            "'!B_class_content!'i"  => isset($B_class_content) && $B_class_content !== '' ? $B_class_content : 'noclass',
            "'!B_content!'i"        => $content
        );

        echo $htvar;

        echo Metalang::metaLang(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent));

        echo '</div>';
    }

    /**
     * Affiche l'éditorial d'un thème.
     *
     * Cherche le fichier `editorial.php` dans le thème actif, puis dans le thème de base.  
     * Remplace le placeholder `!editorial_content!` par le contenu fourni et applique la fonction `Metalang::metaLang()` et `Language::affLangue()`.
     *
     * @param string $content Le contenu à insérer dans l'éditorial.
     * @return string|false Le chemin du fichier inclus, ou false si non trouvé.
     */
    public static function themEdito(string $content): string|false
    {
        global $theme;

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/views/partials/edito/editorial.php')) {
            $inclusion = 'themes/' . $theme . '/views/partials/edito/editorial.php';
        } elseif (file_exists('themes/base/views/partials/edito/editorial.php')) {
            $inclusion = 'themes/base/views/partials/edito/editorial.php';
        } else {
            echo 'editorial.php manquant / not find !<br />';
            die();
        }

        if ($inclusion) {
            ob_start();
            include $inclusion;
            $Xcontent = ob_get_contents();
            ob_end_clean();

            $npds_METALANG_words = array(
                "'!editorial_content!'i"    => $content
            );

            echo Metalang::metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
        }

        return $inclusion;
    }

}
