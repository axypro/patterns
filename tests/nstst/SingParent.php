<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\nstst;

class SingParent
{
    use \axy\patterns\Singleton;

    /**
     * @return \axy\patterns\tests\nstst\SingParent
     */
    public static function getInstance()
    {
        return self::getSingletonInstance();
    }
}
