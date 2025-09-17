<?php

namespace App\Http\Controllers\Front\Forum;

use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Library\Cache\SuperCacheEmpty;
use App\Library\Cache\SuperCacheManager;
use App\Http\Controllers\Core\FrontBaseController;


class Forum extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        $cache_obj = ($SuperCache) ? new SuperCacheManager() :  new SuperCacheEmpty();

        settype($op, 'string');
        settype($Subforumid, 'array');

        if ($op == 'maj_subscribe') {
            if ($user) {
                settype($cookie[0], 'integer');

                $result = sql_query("DELETE FROM " . sql_prefix('subscribe') . " 
                                    WHERE uid='$cookie[0]' 
                                    AND forumid IS NOT NULL");

                $result = sql_query("SELECT forum_id 
                                    FROM " . sql_prefix('forums') . " 
                                    ORDER BY forum_index, forum_id");

                while (list($forumid) = sql_fetch_row($result)) {
                    if (is_array($Subforumid)) {
                        if (array_key_exists($forumid, $Subforumid)) {
                            $resultX = sql_query("INSERT INTO " . sql_prefix('subscribe') . " (forumid, uid) 
                                                VALUES ('$forumid','$cookie[0]')");
                        }
                    }
                }
            }
        }

        //include 'header.php';

        // -- SuperCache
        if (($SuperCache) and (!$user)) {
            $cache_obj->startCachingPage();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($user)) {
            $inclusion = false;

            settype($catid, 'integer');

            if ($catid != '') {
                if (file_exists('themes/' . $theme . '/views/partials/forum/forum-cat$catid.php')) {
                    $inclusion = 'themes/' . $theme . '/views/partials/forum/forum-cat$catid.php';
                } elseif (file_exists('themes/base/views/partials/forum/forum-cat$catid.php')) {
                    $inclusion = 'themes/base/views/partials/forum/forum-cat$catid.php';
                }
            }

            if ($inclusion == false) {
                if (file_exists('themes/' . $theme . '/views/partials/forum/forum-adv.php')) {
                    $inclusion = 'themes/' . $theme . '/views/partials/forum/forum-adv.php';
                } elseif (file_exists('themes/' . $theme . '/views/partials/forum/forum.php')) {
                    $inclusion = 'themes/' . $theme . '/views/partials/forum/forum.php';
                } elseif (file_exists('themes/base/views/partials/forum/forum.php')) {
                    $inclusion = 'themes/base/views/partials/forum/forum.php';
                } else {
                    echo 'views/partials/forum/forum.php / not find !<br />';
                }
            }

            if ($inclusion) {
                $Xcontent = join('', file($inclusion));
                echo Metalang::metaLang(Language::affLangue($Xcontent));
            }
        }

        // -- SuperCache
        if (($SuperCache) and (!$user)) {
            $cache_obj->endCachingPage();
        }

        //include 'footer.php';
    }

}
