<?php

namespace App\Http\Controllers\Front\News;

use App\Http\Controllers\Core\FrontBaseController;


class NewsPrint extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    function PrintPage($oper, $DB, $nl, $sid)
    {
        global $user, $cookie, $theme, $Default_Theme, $language, $site_logo, $sitename, $datetime, $nuke_url, $Titlesitename;

        $aff = true;

        if ($oper == 'news') {
            $xtab = News::newsAff('libre', "WHERE sid='$sid'", 1, 1);

            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[0];

            if ($topic != '') {
                $result2 = sql_query("SELECT topictext 
                                    FROM " . sql_prefix('topics') . " 
                                    WHERE topicid='$topic'");

                list($topictext) = sql_fetch_row($result2);
            } else {
                $aff = false;
            }
        }

        if ($oper == 'archive') {
            $xtab = News::newsAff('archive', "WHERE sid='$sid'", 1, 1);

            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[0];

            if ($topic != '') {
                $result2 = sql_query("SELECT topictext 
                                    FROM " . sql_prefix('topics') . " 
                                    WHERE topicid='$topic'");

                list($topictext) = sql_fetch_row($result2);
            } else {
                $aff = false;
            }
        }

        if ($oper == 'links') {
            $DB = Hack::removeHack(stripslashes(htmlentities(urldecode($DB), ENT_NOQUOTES, 'UTF-8')));

            $result = sql_query("SELECT url, title, description, date 
                                FROM " . $DB . "links_links 
                                WHERE lid='$sid'");

            list($url, $title, $description, $time) = sql_fetch_row($result);

            $title = stripslashes($title);
            $description = stripslashes($description);
        }

        if ($oper == 'static') {
            if (
                preg_match('#^[a-z0-9_\.-]#i', $sid)
                and !stristr($sid, '.*://')
                and !stristr($sid, '..')
                and !stristr($sid, '../')
                and !stristr($sid, 'script')
                and !stristr($sid, 'cookie')
                and !stristr($sid, 'iframe')
                and  !stristr($sid, 'applet')
                and !stristr($sid, 'object')
                and !stristr($sid, 'meta')
            ) {
                if (file_exists('storage/static/' . $sid)) {

                    ob_start();
                    include 'storage/static/' . $sid;
                    $remp = ob_get_contents();
                    ob_end_clean();

                    if ($DB) {
                        $remp = Metalang::metaLang(Code::affCode(Language::affLangue($remp)));
                    }

                    if ($nl) {
                        $remp = nl2br(str_replace(' ', '&nbsp;', htmlentities($remp, ENT_QUOTES, 'UTF-8')));
                    }

                    $title = $sid;
                } else {
                    $aff = false;
                }
            } else {
                $remp = '<div class="alert alert-danger">' . translate('Merci d\'entrer l\'information en fonction des spécifications') . '</div>';
                $aff = false;
            }
        }

        if ($aff == true) {
            $Titlesitename = 'NPDS - ' . translate('Page spéciale pour impression') . ' / ' . $title;

            include 'storage/meta/meta.php';

            if (isset($user)) {
                if ($cookie[9] == '') {
                    $cookie[9] = $Default_Theme;
                }

                if (isset($theme)) {
                    $cookie[9] = $theme;
                }

                $tmp_theme = $cookie[9];

                if (!$file = @opendir('themes/' . $cookie[9])) {
                    $tmp_theme = $Default_Theme;
                }
            } else {
                $tmp_theme = $Default_Theme;
            }

            echo '<link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />';

            echo '</head>
            <body>
                <div max-width="640" class="container p-1 n-hyphenate">
                <div>';

            $pos = strpos($site_logo, '/');

            if ($pos) {
                echo '<img class="img-fluid d-block mx-auto" src="' . $site_logo . '" alt="website logo" />';
            } else {
                echo '<img class="img-fluid d-block mx-auto" src="assets/images/npds/' . $site_logo . '" alt="website logo" />';
            }

            echo '<h1 class="d-block text-center my-4">' . Language::affLangue($title) . '</h1>';

            if (($oper == 'news') or ($oper == 'archive')) {

                $hometext = Metalang::metaLang(Code::affCode(Language::affLangue($hometext)));
                $bodytext = Metalang::metaLang(Code::affCode(Language::affLangue($bodytext)));

                echo '<span class="float-end" style="font-size: .8rem;"> ' . Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT) . '</span><br />
                    <hr />
                    <h2 class="mb-3">' . translate('Sujet : ') . ' ' . Language::affLangue($topictext) . '</h2>
                </div>
                <div>' . $hometext . '<br /><br />';

                if ($bodytext != '') {
                    echo $bodytext . '<br /><br />';
                }

                echo Metalang::metaLang(Code::affCode(Language::affLangue($notes)));

                echo '</div>';

                if ($oper == 'news') {
                    echo '<hr />
                    <p class="text-center">' . translate('Cet article provient de') . ' ' . $sitename . '<br />
                    ' . translate('L\'url pour cet article est : ') . '
                    <a href="' . $nuke_url . '/article.php?sid=' . $sid . '">' . $nuke_url . '/article.php?sid=' . $sid . '</a>
                    </p>';
                } else {
                    echo '<hr />
                    <p class="text-center">' . translate('Cet article provient de') . ' ' . $sitename . '<br />
                    ' . translate('L\'url pour cet article est : ') . '
                    <a href="' . $nuke_url . '/article.php?sid=' . $sid . '&amp;archive=1">' . $nuke_url . '/article.php?sid=' . $sid . '&amp;archive=1</a>
                    </p>';
                }
            }

            if ($oper == 'links') {
                echo '<span class="float-end" style="font-size: .8rem;">' . Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT) . '</span><br /><hr />';

                if ($url != '') {
                    echo '<h2 class="mb-3">' . translate('Liens') . ' : ' . $url . '</h2>';
                }

                echo '<div>' . Language::affLangue($description) . '</div>
                <hr />
                <p class="text-center">' . translate('Cet article provient de') . ' ' . $sitename . '<br />
                <a href="' . $nuke_url . '">' . $nuke_url . '</a></p>';
            }

            if ($oper == 'static') {
                echo '<div>
                    ' . $remp . '
                </div>
                <hr />
                <p class="text-center">' . translate('Cet article provient de') . ' ' . $sitename . '<br />
                <a href="' . $nuke_url . '/static.php?op=' . $sid . '&npds=1">' . $nuke_url . '/static.php?op=' . $sid . '&npds=1</a></p>';
            }

            echo '</div>
            </body>
            </html>';
        } else {
            header('location: index.php');
        }
    }

    public function index()
    {
        if (!empty($sid)) {
            $tab = explode(':', $sid);

            if ($tab[0] == 'static') {

                settype($metalang, 'integer');
                settype($nl, 'integer');

                PrintPage('static', $metalang, $nl, $tab[1]);
            } else {
                if (!isset($archive)) {
                    PrintPage('news', '', '', $sid);
                } else {
                    PrintPage('archive', '', '', $sid);
                }
            }
        } elseif (!empty($lid)) {
            settype($lid, 'integer');

            PrintPage('links', $DB, '', $lid);
        } else {
            header('location: index.php');
        }
    }

}
