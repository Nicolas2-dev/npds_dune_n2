<?php

namespace App\Library\Theme;

use IntlDateFormatter;
use Npds\Config\Config;
use App\Support\Facades\Css;
use App\Support\Facades\Date;
use App\Support\Facades\User;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;


class Theme
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    
    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * 
     *
     * @return  [type]  [return description]
     */
    public function loadConfig(): void
    {
        $theme_lists =  $this->themeList();

        $themeArray = explode(' ', $theme_lists);

        foreach ($themeArray  as $theme) {

            foreach (glob(theme_path($theme . '/Config/*.php')) as $path) {
                $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));
                Config::set('theme_' . strtolower($theme) . '.'. $key, require($path));
            }
        }
    }

        /**
     * Récupère une valeur de configuration spécifique au thème courant.
     *
     * @param string $key Clé de configuration relative au thème (ex: ".theme.name")
     * @param mixed  $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    public function theme_config(string $key = '', mixed $default = null): mixed
    {
        $theme = Theme::getTheme();
        $configKey = 'theme_' . strtolower($theme) . ($key !== '' ? '.' . $key : '');

        if (Config::has($configKey)) {
            return Config::get($configKey, $default);
        }

        return $default;
    }

    /**
     * Récupère le thème actif pour l'utilisateur.
     *
     * Cette méthode vérifie si l'utilisateur est connecté et si un cookie contient un thème personnalisé.
     * Si le thème indiqué dans le cookie n'existe pas, le thème par défaut est retourné.
     *
     * @return string Le nom du thème actif
     */
    public function getTheme(): string
    {
        global $user, $cookie;

        $defaultTheme = Config::get('theme.Default_Theme');

        if (isset($user) && $user !== '') {
            if (!empty($cookie[9])) {
                $ibix = explode('+', urldecode($cookie[9]));
                $theme = $ibix[0] ?? $defaultTheme;

                // Vérifie que le répertoire du thème existe
                if (@opendir('themes/' . $theme)) {
                    return $theme;
                }
            }
        }

        return $defaultTheme;
    }

    /**
     * Récupère le skin actif pour l'utilisateur.
     *
     * Cette méthode vérifie si l'utilisateur est connecté et si un cookie contient un skin personnalisé.
     * Si aucun skin n'est défini dans le cookie, le skin par défaut est retourné.
     *
     * @return string Le nom du skin actif
     */
    public function getSkin(): string
    {
        global $user, $cookie;

        $defaultSkin = Config::get('theme.Default_Skin');

        if (isset($user) && $user !== '') {
            if (!empty($cookie[9])) {
                $ibix = explode('+', urldecode($cookie[9]));
                return $ibix[1] ?? $defaultSkin;
            }
        }

        return $defaultSkin;
    }

    /**
     * Retourne le chemin complet de l'image si elle existe dans le répertoire du thème.
     *
     * @param string $theme_img Nom du fichier image
     * @return string|false Chemin complet si trouvé, sinon false
     */
    public function image(string $theme_img): string|false
    {
        global $theme; // global a revoir !

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
    public function themeImage($theme_img)
    {
        return $this->image($theme_img);
    }

    /**
     * Retourne la liste des thèmes disponibles dans le dossier 'themes'.
     *
     * Les dossiers commençant par "_" ou contenant "base" ou un "." sont ignorés.
     *
     * @return string Liste des thèmes séparés par un espace
     */
    public function themeList(): string
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
    public function localVar(string $Xcontent): ?string
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
     * @param int|string $time Timestamp
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
    public function themeIndex(
        string      $aid,
        string      $informant,
        int|string  $time,
        string|null $title,
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
        global $tipath, $theme; // global a revoir !

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/Views/Partials/News/IndexNews.php')) {
            $inclusion = 'themes/' . $theme . '/Views/Partials/News/IndexNews.php';
        } elseif (file_exists('themes/Base/Views/Partials/News/IndexNews.php')) {
            $inclusion = 'themes/Base/Views/Partials/News/IndexNews.php';
        } else {
            echo 'IndexNews.php manquant / not find !<br />';
            die();
        }

        $H_var = $this->localVar($thetext);

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
        //echo Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent));
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
    public function themeArticle(
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
        global $tipath, $theme, $counter, $boxtitle, $boxstuff; // global a revoir 

        $inclusion = false;

        if (file_exists("themes/" . $theme . "/Views/Partials/News/DetailNews.php")) {
            $inclusion = "themes/" . $theme . "/Views/Partials/News/DetailNews.php";
        } elseif (file_exists("themes/Base/Views/Partials/News/DetailNews.php")) {
            $inclusion = "themes/Base/Views/Partials/News/DetailNews.php";
        } else {
            echo 'detail-news.php manquant / not find !<br />';
            die();
        }

        $H_var = $this->localVar($thetext);

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
    public function themeSidebox(string $title, string $content): void
    {
        global $theme, $B_class_title, $B_class_content, $bloc_side, $htvar; // global a revoir !

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/Views/Partials/Block/BlocRight.php') && ($bloc_side == 'RIGHT')) {
            $inclusion = 'themes/' . $theme . '/Views/Partials/Block/BlocRight.php';
        }

        if (file_exists('themes/' . $theme . '/Views/Partials/Block/BlocLeft.php') && ($bloc_side == 'LEFT')) {
            $inclusion = 'themes/' . $theme . '/Views/Partials/Block/BlocLeft.php';
        }

        if (!$inclusion) {
            if (file_exists('themes/' . $theme . '/Views/Partials/Block/Bloc.php')) {
                $inclusion = 'themes/' . $theme . '/Views/Partials/Block/Bloc.php';
            } elseif (file_exists('themes/Base/Views/Partials/Block/Bloc.php')) {
                $inclusion = 'themes/Base/Views/Partials/Block/Bloc.php';
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
    public function themEdito(string $content): string|false
    {
        global $theme; // global a revoir !

        $inclusion = false;

        if (file_exists('themes/' . $theme . '/Views/Partials/Edito/Editorial.php')) {
            $inclusion = 'themes/' . $theme . '/Views/Partials/Edito/Editorial.php';
        } elseif (file_exists('themes/Base/Views/Partials/Edito/Editorial.php')) {
            $inclusion = 'themes/Base/Views/Partials/Edito/Editorial.php';
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

    /**
     * Génère un bloc HTML pour un lien "Plus de contenu" avec collapse Bootstrap.
     *
     * @param string $coltarget L’ID ou sélecteur de l’élément collapsible ciblé.
     * @return void HTML du bloc collapsible
     */
    public function colsyst(string $coltarget): void
    {
        echo '<div class="col d-lg-none me-2 my-2">
            <hr />
            <a class=" small float-end" href="#" data-bs-toggle="collapse" data-bs-target="' . $coltarget . '">
                <span class="plusdecontenu trn">Plus de contenu</span>
            </a>
        </div>';
    }


    // deprecated !
    public function getThemeNpds()
    {
        // take the right theme location !
        global $user; // global a revoir !

        $Default_Theme = Config::get('theme.Default_Theme');
        $Default_Skin = Config::get('theme.Default_Skin');
        
        if (isset($user) and $user != '') {

            global $cookie;
            if ($cookie[9] != '') {
                $ibix = explode('+', urldecode($cookie[9]));

                if (array_key_exists(0, $ibix)) {
                    $theme = $ibix[0];
                } else {
                    $theme = $Default_Theme;
                }

                if (array_key_exists(1, $ibix)) {
                    $skin = $ibix[1];
                } else {
                    $skin = $Default_Skin;
                }

                $tmp_theme = $theme;

                if (!$file = @opendir('themes/' . $theme)) {
                    $tmp_theme = $Default_Theme;
                }
            } else {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $theme = $Default_Theme;
            $skin = $Default_Skin;
            $tmp_theme = $theme;
        }
    }

    ////////// provisoire a revoir /////////

    // Note toutes ces function sont a revoir et a déprécier !!

    //function head($tiny_mce_init, $css_pages_ref, $css, $tmp_theme, $skin, $js, $m_description, $m_keywords)
    function head()
    {
        // global a revoir !
        //global $slogan, $Titlesitename, $banners, $Default_Theme, $theme, $gzhandler; 
        //global $language, $topic, $hlpfile, $user, $hr, $long_chain, $theme_darkness;

        //$tmp_theme = $this->getTheme();
        //$skin = $this->getSkin();

        //settype($m_keywords, 'string');
        //settype($m_description, 'string');

        //if ($gzhandler == 1)
        //    ob_start('ob_gzhandler');

        //include 'themes/' . $tmp_theme . '/views/theme.php';

        // Meta
        //if (file_exists(storage_PATH('meta/meta.php'))) {
        //    $meta_op = '';
        //    include storage_PATH('meta/meta.php');
        //}

        // Favicon
        //$favico = (file_exists('themes/' . $tmp_theme . '/assets/images/favicon/favicon.ico'))
        //    ? 'themes/' . $tmp_theme . '/assets/images/favicon/favicon.ico'
        //    : 'assets/images/favicon/favicon.ico';
        //
        //echo '
        //<link rel="shortcut icon" href="' . $favico . '" type="image/x-icon" />
        //<link rel="apple-touch-icon" sizes="120x120" href="assets/images/favicon/favicon-120.png" />
        //<link rel="apple-touch-icon" sizes="152x152" href="assets/images/favicon/favicon-152.png" />
        //<link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/favicon-180.png" />';

        // Syndication RSS & autres
        //global $sitename, $nuke_url; // global a revoir !

        // Canonical
        //$scheme = strtolower($_SERVER['REQUEST_SCHEME'] ?? 'http');
        //$host = $_SERVER['HTTP_HOST'];
        //$uri = $_SERVER['REQUEST_URI'];

        //echo '<link rel="canonical" href="' . Request::url() . '" />';

        // humans.txt
        //if (file_exists('humans.txt')) {
        //    echo '<link type="text/plain" rel="author" href="' . $nuke_url . '/humans.txt" />';
        //}

        //echo '<link href="backend.php?op=RSS0.91" title="' . $sitename . ' - RSS 0.91" rel="alternate" type="text/xml" />
        //<link href="backend.php?op=RSS1.0" title="' . $sitename . ' - RSS 1.0" rel="alternate" type="text/xml" />
        //<link href="backend.php?op=RSS2.0" title="' . $sitename . ' - RSS 2.0" rel="alternate" type="text/xml" />
        //<link href="backend.php?op=ATOM" title="' . $sitename . ' - ATOM" rel="alternate" type="application/atom+xml" />';

        // Tiny_mce
        //Editeur::start()

        // include externe JAVASCRIPT file from modules/include or themes/.../include for functions, codes in the <body onload="..." event...
        $body_onloadH = '
        <script type="text/javascript">
            //<![CDATA[
                function init() {';

        $body_onloadF = '
                }
            //]]>
        </script>';

        if (file_exists('themes/Base/Bootstrap/body_onload.php')) {
            echo $body_onloadH;
            include 'themes/Base/Bootstrap/body_onload.php';
            echo $body_onloadF;
        }

        if (file_exists('themes/' . $tmp_theme . '/Bootstrap/body_onload.php')) {
            echo $body_onloadH;
            include 'themes/' . $tmp_theme . '/Bootstrap/body_onload.php';
            echo $body_onloadF;
        }

        // deprecated file : 
        // themes/Base/Bootstrap/header_head.php
        // themes/' . $tmp_theme . '/Bootstrap/header_head.php


        // include externe file from themes/base/bootstrap/ || themes/.../bootstrap/ for functions, codes ... - skin motor
        //if (file_exists('themes/Base/Bootstrap/header_head.php')) {
        //
        //    ob_start();
        //    include 'themes/Base/Bootstrap/header_head.php';
        //    $hH = ob_get_contents();
        //    ob_end_clean();
        //
        //    if ($skin != '' and substr($tmp_theme, -3) == '_sk') {
        //        $hH = str_replace('assets/shared/bootstrap/dist/css/bootstrap.min.css', 'assets/skins/' . $skin . '/bootstrap.min.css', $hH);
        //        $hH = str_replace('assets/shared/bootstrap/dist/css/extra.css', 'assets/skins/' . $skin . '/extra.css', $hH);
        //    }
        //
        //    echo $hH;
        //}

        //if (file_exists('themes/' . $tmp_theme . '/Bootstrap/header_head.php')) {
        //    include 'themes/' . $tmp_theme . '/Bootstrap/header_head.php';
        //}

        //global $css_pages_ref, $css, $js;

        //echo Css::importCss($tmp_theme, $language, '', $css_pages_ref, $css);

        // Mod by Jireck - Chargeur de JS via PAGES.PHP
        // PageRef::js();


        //echo '</head>';

        //include THEME_PATH . $tmp_theme . '/Views/layouts/header.php';
    }

    // function a revoir suite a suppression des global !
    function footmsg()
    {
        global $foot1, $foot2, $foot3, $foot4; // global a revoir !

        $foot = '<p align="center">';

        // Boucle sur les variables $foot1 à $foot4
        for ($i = 1; $i <= 4; $i++) {
            $varName = 'foot' . $i;
            if (!empty($$varName)) {
                $foot .= stripslashes($$varName);
                if ($i < 4) {
                    $foot .= '<br />';
                }
            }
        }

        $foot .= '</p>';

        echo Language::affLangue($foot);
    }

    function foot()
    {
        //global $user, $Default_Theme, $cookie9; // global a revoir !
        //
        //if ($user) {
        //    $cookie = explode(':', base64_decode($user));
        //
        //    if ($cookie[9] == '') {
        //        $cookie[9] = $Default_Theme;
        //    }
        //
        //    $ibix = explode('+', urldecode($cookie[9]));
        //
        //    if (!@opendir(THEME_PATH . $ibix[0])) {
        //        $theme = $Default_Theme;
        //    } else {
        //        $theme = $ibix[0];
        //    }
        //} else {
        //    $theme = $Default_Theme;
        //}

        // include 'themes/' . $theme . '/Views/footer.php';

        // if ($user) {
        //     $cookie9 = $ibix[0];
        // }
    }

    function footer_after($theme)
    {
        if (file_exists($path_theme = 'themes/' . $theme . '/Views/Bootstrap/Footer_after.php')) {
            include $path_theme;
        } else {
            if (file_exists($path_module = 'themes/Base/Views/Bootstrap/ooter_after.php')) {
                include $path_module;
            }
        }
    }

    function footer_before()
    {
        if (file_exists($path_module = 'themes/Base/Views/Bootstrap/Footer_before.php')) {
            include $path_module;
        }
    }

}
