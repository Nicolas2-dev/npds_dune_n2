<?php

/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://backend.userland.com/rss
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */

namespace Shared\Feed;

use Shared\Feed\RSSCreator091;


class RSSCreator20 extends RSSCreator091
{

    public function __construct()
    {
        parent::_setRSSVersion('2.0');
    }
    
}
