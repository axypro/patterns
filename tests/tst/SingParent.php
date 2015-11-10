<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\tst;

use axy\patterns\Singleton;

class SingParent
{
    use Singleton;

    /**
     * @return \axy\patterns\tests\tst\SingParent
     */
    public static function getInstance()
    {
        return self::getSingletonInstance();
    }
}
