<?php

namespace App\Http\Controllers\Front\Forum;

use App\Support\Facades\Url;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Http\Controllers\Core\FrontBaseController;


class Topics extends FrontBaseController
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
        if ($op != 'maj_subscribe') {

            //include 'header.php';

            $inclusion = false;

            if (file_exists('themes/' . $theme . '/views/partials/news/topics.php')) {
                $inclusion = 'themes/' . $theme . '/views/partials/news/topics.php';
            } elseif (file_exists('themes/base/views/partials/news/topics.php')) {
                $inclusion = 'themes/base/views/partials/news/topics.php';
            } else {
                echo 'views/partials/news/topics.html / not find !<br />';
            }

            if ($inclusion) {
                ob_start();
                include($inclusion);

                $Xcontent = ob_get_contents();
                ob_end_clean();
                echo Metalang::metaLang(Language::affLangue($Xcontent));
            }

            //include 'footer.php';
        } else {
            if ($subscribe) {
                if ($user) {
                    $result = sql_query("DELETE FROM " . sql_prefix('subscribe') . " 
                                        WHERE uid='$cookie[0]' 
                                        AND topicid IS NOT NULL");

                    $selection = sql_query("SELECT topicid 
                                            FROM " . sql_prefix('topics') . " 
                                            ORDER BY topicid");

                    while (list($topicid) = sql_fetch_row($selection)) {
                        if (isset($Subtopicid)) {
                            if (array_key_exists($topicid, $Subtopicid)) {
                                if ($Subtopicid[$topicid] == "on") {
                                    $resultX = sql_query("INSERT INTO " . sql_prefix('subscribe') . " (topicid, uid) 
                                                        VALUES ('$topicid','$cookie[0]')");
                                }
                            }
                        }
                    }

                    Url::redirectUrl('topics.php');
                }
            }
        }
    }

}
