<?php

/**
 * Classe enum basique pour FeedCreator
 * 
 * // Utilisation
 * echo FeedCreatorConfig::TIME_ZONE;
 * echo FeedCreatorConfig::VERSION;
 * 
 */
class FeedCreatorConfig
{
    const TIME_ZONE = '';
    const VERSION = 'FeedCreator 2.0 for NPDS';
    
    // Empêcher l'instanciation
    private function __construct() {}
}
