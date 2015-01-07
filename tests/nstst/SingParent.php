<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\nstst;

use axy\patterns\Singleton;

class SingParent
{
    use Singleton;

    /**
     * @return \axy\patterns\tests\nstst\SingParent
     */
    public static function getInstance()
    {
        return self::getSingletonInstance();
    }
}
