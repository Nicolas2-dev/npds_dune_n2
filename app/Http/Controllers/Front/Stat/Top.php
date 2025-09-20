<?php

namespace App\Http\Controllers\Front\Stat;

use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Library\Cache\SuperCacheEmpty;
use App\Library\Cache\SuperCacheManager;
use App\Http\Controllers\Core\FrontBaseController;


class Top extends FrontBaseController
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
        $cache_obj = $SuperCache ? new SuperCacheManager() : new SuperCacheEmpty();

        if (($SuperCache) and (!$user)) {
            $cache_obj->startCachingPage();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($user)) {
            $inclusion = false;
            if (file_exists($path = 'themes/' . $theme . '/views/partials/topn/top.php')) {
                $inclusion = $path;
            } elseif (file_exists($path = 'themes/base/views/partials/top/top.php')) {
                $inclusion = $path;
            } else {
                echo 'views/partials/top/top.php / not find !<br />';
            }

            if ($inclusion) {
                ob_start();
                include $inclusion;
                $Xcontent = ob_get_contents();
                ob_end_clean();

                echo Metalang::metaLang(Language::affLangue($Xcontent));
            }
        }

        // -- SuperCache
        if (($SuperCache) and (!$user)) {
            $cache_obj->endCachingPage();
        }
    }
    
}
