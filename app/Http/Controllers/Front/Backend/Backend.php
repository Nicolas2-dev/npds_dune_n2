<?php

namespace App\Http\Controllers\Front\Backend;

use Npds\Config\Config;
use Npds\Http\Response;
use Shared\Feed\FeedItem;
use Shared\Feed\FeedImage;
use App\Support\Facades\Date;
use App\Support\Facades\News;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use Shared\Feed\FeedCreatorConfig;
use Shared\Feed\UniversalFeedCreator;
use App\Http\Controllers\Core\BaseController;


class Backend extends BaseController
{

    /**
     * Durée maximale du cache en secondes.
     *
     * @var int
     */
    protected int $timeout = 3600;


    /**
     * Méthode exécutée avant toute action du contrôleur.
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * Point d’entrée du backend : génère le flux demandé selon l’option passée.
     *
     * @param   string|null  $op  Type de flux demandé (RSS2.0, ATOM, OPML, etc.)
     *
     * @return  Response     Réponse HTTP contenant le flux généré
     */
    public function index(?string $op = ''): Response
    {
        $op = strtoupper($op);

        return match ($op) {
            'MBOX'    => $this->fabFeed('MBOX', 'MBOX-feed.xml'),
            'OPML'    => $this->fabFeed('OPML', 'OPML-feed.xml'),
            'ATOM'    => $this->fabFeed('ATOM', 'ATOM-feed.xml'),
            'RSS1.0'  => $this->fabFeed('RSS1.0', 'RSS1.0-feed.xml'),
            'RSS2.0'  => $this->fabFeed('RSS2.0', 'RSS2.0-feed.xml'),
            'RSS0.91' => $this->fabFeed('RSS0.91', 'RSS0.91-feed.xml'),
            default   => $this->fabFeed('RSS1.0', 'RSS1.0-feed.xml'),
        };
    }

    /**
     * Génère un flux au format demandé et le retourne sous forme de réponse HTTP.
     *
     * @param   string  $type      Type du flux (RSS2.0, ATOM, OPML, etc.)
     * @param   string  $filename  Nom du fichier de cache associé au flux
     *
     * @return  Response           Réponse HTTP contenant le flux généré
     */
    private function fabFeed(string $type, string $filename): Response
    {
        FeedCreatorConfig::setTimeZone('Europe/Paris');
        FeedCreatorConfig::setVersion('FeedCreator 2.1 for NPDS');

        $rss = new UniversalFeedCreator();

        $path = storage_path('feed/');

        $rss->useCached($type, $path . $filename, $this->timeout);

        $rss->title                     = Config::get('app.sitename');
        $rss->description               = Config::get('app.slogan');
        $rss->descriptionTruncSize      = 250;
        $rss->descriptionHtmlSyndicated = true;

        $rss->link           = site_url();
        $rss->syndicationURL = site_url('backend/' . $type);

        $image = new FeedImage();
        $image->title           = Config::get('app.sitename');
        $image->url             = Config::get('backand.backend_image');
        $image->link            = site_url();
        $image->description     = Config::get('backend.backend_title');
        $image->width           = Config::get('backend.backend_width');
        $image->height          = Config::get('backend.backend_height');
        $rss->image = $image;

        $storyhome = (int) Config::get('stories.storyhome');

        $xtab = News::newsAff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');

        $story_limit = 0;

        while (($story_limit < $storyhome) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $item = new FeedItem();

            $item->title    = Language::previewLocalLangue(Config::get('backand.backend_language'), str_replace('&quot;', '\"', $title));
            $item->link     = site_url('article/' . $sid);

            $item->description               = Metalang::metaLang(Language::previewLocalLangue(Config::get('backand.backend_language'), $hometext));
            $item->descriptionHtmlSyndicated = true;

            $item->date = strtotime(Date::getPartOfTime($time, 'yyyy-MM-dd H:m:s'));

            $item->source = site_url();
            $item->author = $aid;

            $rss->addItem($item);
        }

        // Génère le flux
        $content = $rss->saveFeed($type, $path . $filename);

        return (new Response($content));
            //->header(
            //    'Content-Type',
            //    in_array(strtoupper($type), ['RSS2.0', 'RSS1.0', 'RSS0.91'])
            //        ? 'application/rss+xml; charset=UTF-8'
            //        : 'application/xml; charset=UTF-8'
            //);
    }

}
