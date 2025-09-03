<?php

namespace Shared\Feed;


class FeedCreatorConfig
{
    
    /**
     * Fuseau horaire utilisé par le générateur de flux.
     *
     * @var string
     */
    private static $timeZone = '';

    /**
     * Version actuelle de la classe FeedCreator.
     *
     * @var string
     */
    private static $version = 'FeedCreator 2.0 for NPDS';


    /**
     * Empêche l'instanciation de cette classe (singleton ou classe statique).
     *
     * La construction directe de l'objet est privée afin de forcer l'utilisation
     * des méthodes statiques.
     */
    private function __construct() {}

    /**
     * Getter pour TIME_ZONE
     */
    public static function getTimeZone(): string
    {
        return self::$timeZone;
    }
    
    /**
     * Setter pour TIME_ZONE
     */
    public static function setTimeZone(string $timeZone): void
    {
        self::$timeZone = $timeZone;
    }
    
    /**
     * Getter pour VERSION
     */
    public static function getVersion(): string
    {
        return self::$version;
    }
    
    /**
     * Setter pour VERSION
     */
    public static function setVersion(string $version): void
    {
        self::$version = $version;
    }

}
