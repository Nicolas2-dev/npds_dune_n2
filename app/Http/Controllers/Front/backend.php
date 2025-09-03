<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\Date\Date;
use App\Library\News\News;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;

include 'mainfile.php';

// note a revoir pour les namespace !

include 'Library\Feed\HtmlDescribable.php';
include 'Library\Feed\FeedCreator.php';
include 'Library\Feed\AtomCreator03.php';
include 'Library\Feed\FeedCreatorConfig.php';
include 'Library\Feed\FeedDate.php';
include 'Library\Feed\FeedHtmlField.php';
include 'Library\Feed\FeedImage.php';
include 'Library\Feed\FeedItem.php';
include 'Library\Feed\MBOXCreator.php';
include 'Library\Feed\OPMLCreator.php';
include 'Library\Feed\RSSCreator091.php';
include 'Library\Feed\RSSCreator10.php';
include 'Library\Feed\RSSCreator20.php';
include 'Library\Feed\UniversalFeedCreator.php';

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

// Format : RSS0.91, RSS1.0, RSS2.0, MBOX, OPML, ATOM
settype($op, 'string');

$op = strtoupper($op);

switch ($op) {

    case 'MBOX':
        fab_feed('MBOX', 'MBOX-feed', 3600);
        break;

    case 'OPML':
        fab_feed('OPML', 'OPML-feed.xml', 3600);
        break;

    case 'ATOM':
        fab_feed('ATOM', 'ATOM-feed.xml', 3600);
        break;

    case 'RSS1.0':
        fab_feed('RSS1.0', 'RSS1.0-feed.xml', 3600);
        break;

    case 'RSS2.0':
        fab_feed('RSS2.0', 'RSS2.0-feed.xml', 3600);
        break;

    case 'RSS0.91':
        fab_feed('RSS0.91', 'RSS0.91-feed.xml', 3600);
        break;

    default:
        fab_feed('RSS1.0', 'RSS1.0-feed.xml', 3600);
        break;
}
