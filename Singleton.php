<?php
/**
 * @package axy\patterns
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\patterns;

/**
 * The trait for creating a singleton
 */
trait Singleton
{
    /**
     * @return object
     */
    public static function getInstance()
    {
        return self::getSingletonInstance();
    }

    /**
     * @return object
     */
    protected static function getSingletonInstance()
    {
        if (!static::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The singleton instance
     *
     * @var object
     */
    private static $instance;
}
