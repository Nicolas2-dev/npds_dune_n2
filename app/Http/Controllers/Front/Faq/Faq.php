<?php

namespace App\Http\Controllers\Front\Faq;

use App\Support\Security\Hack;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Library\Cache\SuperCacheEmpty;
use App\Library\Cache\SuperCacheManager;
use App\Http\Controllers\Core\FrontBaseController;


class Faq extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    function ShowFaq($id_cat, $categories)
    {
        echo '<h2 class="mb-4">' . translate('FAQ - Questions fréquentes') . '</h2>
            <hr />
            <h3 class="mb-3">' . translate('Catégorie') . ' <span class="text-body-secondary"># ' . StripSlashes($categories) . '</span></h3>
            <p class="lead">
                <a href="faq.php" title="' . translate('Retour à l\'index FAQ') . '" data-bs-toggle="tooltip">Index</a>&nbsp;&raquo;&raquo;&nbsp;' . StripSlashes($categories) . '
            </p>';

        $result = sql_query("SELECT id, id_cat, question, answer 
                            FROM " . sql_prefix('faqanswer') . " 
                            WHERE id_cat='$id_cat'");

        while (list($id, $id_cat, $question, $answer) = sql_fetch_row($result)) {
        }
    }

    function ShowFaqAll($id_cat)
    {
        $result = sql_query("SELECT id, id_cat, question, answer 
                            FROM " . sql_prefix('faqanswer') . " 
                            WHERE id_cat='$id_cat'");

        while (list($id, $id_cat, $question, $answer) = sql_fetch_row($result)) {
            echo '<div class="card mb-3" id="accordion_' . $id . '" role="tablist" aria-multiselectable="true">
                <div class="card-body">
                    <h4 class="card-title">
                    <a data-bs-toggle="collapse" data-parent="#accordion_' . $id . '" href="#faq_' . $id . '" aria-expanded="true" aria-controls="' . $id . '"><i class="fa fa-caret-down toggle-icon"></i></a>&nbsp;' . Language::affLangue($question) . '
                    </h4>
                    <div class="collapse" id="faq_' . $id . '" >
                    <div class="card-text">
                    ' . Metalang::metaLang(Language::affLangue($answer)) . '
                    </div>
                    </div>
                </div>
            </div>';
        }
    }

    public function index()
    {
        if (!$myfaq) {

            //include 'header.php';

            // Include cache manager
            if ($SuperCache) {
                $cache_obj = new SuperCacheManager();
                $cache_obj->startCachingPage();
            } else {
                $cache_obj = new SuperCacheEmpty();
            }

            if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
                $result = sql_query("SELECT id_cat, categories 
                                    FROM " . sql_prefix('faqcategories') . " 
                                    ORDER BY id_cat ASC");

                echo '<h2 class="mb-4">' . translate('FAQ - Questions fréquentes') . '</h2>
                <hr />
                <h3 class="mb-3">' . translate('Catégories') . '<span class="badge bg-secondary float-end">' . sql_num_rows($result) . '</span></h3>
                <div class="list-group">';

                while (list($id_cat, $categories) = sql_fetch_row($result)) {
                    $catname = urlencode(Language::affLangue($categories));
                    echo '<a class="list-group-item list-group-item-action" href="faq.php?id_cat=' . $id_cat . '&amp;myfaq=yes&amp;categories=' . $catname . '"><h4 class="list-group-item-heading">' . Language::affLangue($categories) . '</h4></a>';
                }

                echo '</div>';
            }

            if ($SuperCache) {
                $cache_obj->endCachingPage();
            }

            //include 'footer.php';
        } else {
            $title = 'FAQ : ' . Hack::removeHack(StripSlashes($categories));

            //include 'header.php';

            // Include cache manager
            if ($SuperCache) {
                $cache_obj = new SuperCacheManager();
                $cache_obj->startCachingPage();
            } else {
                $cache_obj = new SuperCacheEmpty();
            }

            if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {
                ShowFaq($id_cat, Hack::removeHack($categories));
                ShowFaqAll($id_cat);
            }

            if ($SuperCache) {
                $cache_obj->endCachingPage();
            }

            //include 'footer.php';
        }
    }

}
