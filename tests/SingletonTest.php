<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests;

use axy\patterns\tests\nstst\SingParent;
use axy\patterns\tests\nstst\SingChild;

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
        $this->assertSame('axy\patterns\tests\nstst\SingChild', \get_class($child));
        $parent = SingParent::getInstance();
        $this->assertSame('axy\patterns\tests\nstst\SingParent', \get_class($parent));
        $this->assertSame($child, SingChild::getInstance());
        $this->assertSame($parent, SingParent::getInstance());
    }
}
