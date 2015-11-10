<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests;

use axy\patterns\tests\tst\SingParent;
use axy\patterns\tests\tst\SingChild;

/**
 * coversDefaultClass axy\patterns\Singleton
 */
class SingletonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::getInstance
     */
    public function testGetInstance()
    {
        $child = SingChild::getInstance();
        $this->assertSame('axy\patterns\tests\tst\SingChild', \get_class($child));
        $parent = SingParent::getInstance();
        $this->assertSame('axy\patterns\tests\tst\SingParent', \get_class($parent));
        $this->assertSame($child, SingChild::getInstance());
        $this->assertSame($parent, SingParent::getInstance());
    }
}
