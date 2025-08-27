<?php

class FeedCreatorConfig
{
    
    private static $timeZone = '';

    
    private static $version = 'FeedCreator 2.0 for NPDS';
    
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
    
    // Empêcher l'instanciation
    private function __construct() {}
}
