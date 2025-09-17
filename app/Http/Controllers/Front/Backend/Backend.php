<?php

namespace App\Http\Controllers\Front\Backend;

use Shared\Feed\FeedItem;
use Shared\Feed\FeedImage;
use App\Support\Facades\Date;
use App\Support\Facades\News;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use Shared\Feed\FeedCreatorConfig;
use Shared\Feed\UniversalFeedCreator;
use App\Http\Controllers\Core\FrontBaseController;


class Backend extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    // note a revoir pour les namespace !

    function fab_feed($type, $filename, $timeout)
    {
        global $sitename, $slogan, $nuke_url, $backend_image, $backend_title, $backend_width, $backend_height, $backend_language, $storyhome;

        FeedCreatorConfig::setTimeZone('Europe/Paris');
        FeedCreatorConfig::setVersion('FeedCreator 2.1 for NPDS');

        $path = 'storage/feed/';

        $rss = new UniversalFeedCreator();
        $rss->useCached($type, $path . $filename, $timeout);

        $rss->title = $sitename;
        $rss->description = $slogan;
        $rss->descriptionTruncSize = 250;
        $rss->descriptionHtmlSyndicated = true;

        $rss->link = $nuke_url;
        $rss->syndicationURL = $nuke_url . '/backend.php?op=' . $type;

        $image = new FeedImage();
        $image->title = $sitename;
        $image->url = $backend_image;
        $image->link = $nuke_url;
        $image->description = $backend_title;
        $image->width = $backend_width;
        $image->height = $backend_height;
        $rss->image = $image;

        $xtab = News::newsAff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');

        $story_limit = 0;

        while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $item = new FeedItem();
            $item->title = Language::previewLocalLangue($backend_language, str_replace('&quot;', '\"', $title));
            $item->link = $nuke_url . '/article.php?sid=' . $sid;

            $item->description = Metalang::metaLang(Language::previewLocalLangue($backend_language, $hometext));
            $item->descriptionHtmlSyndicated = true;

            $item->date = strtotime(Date::getPartOfTime($time, 'yyyy-MM-dd H:m:s'));

            $item->source = $nuke_url;
            $item->author = $aid;
            $rss->addItem($item);
        }

        echo $rss->saveFeed($type, $path . $filename);
    }

    public function index()
    {
        $op = strtoupper($op);

        switch ($op) {

            case 'MBOX':
                $this->fab_feed('MBOX', 'MBOX-feed', 3600);
                break;

            case 'OPML':
                $this->fab_feed('OPML', 'OPML-feed.xml', 3600);
                break;

            case 'ATOM':
                $this->fab_feed('ATOM', 'ATOM-feed.xml', 3600);
                break;

            case 'RSS1.0':
                $this->fab_feed('RSS1.0', 'RSS1.0-feed.xml', 3600);
                break;

            case 'RSS2.0':
                $this->fab_feed('RSS2.0', 'RSS2.0-feed.xml', 3600);
                break;

            case 'RSS0.91':
                $this->fab_feed('RSS0.91', 'RSS0.91-feed.xml', 3600);
                break;

            default:
                $this->fab_feed('RSS1.0', 'RSS1.0-feed.xml', 3600);
                break;
        }
    }

}
