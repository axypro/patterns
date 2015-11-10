<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests;

use axy\patterns\tests\tst\TContainer;
use axy\patterns\tests\tst\ContainerNoContext;

/**
 * coversDefaultClass axy\patterns\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::__isset
     */
    public function testIsset()
    {
        $container = new TContainer();
        $this->assertTrue(isset($container->one));
        $this->assertTrue(isset($container->four));
        $this->assertTrue(isset($container->mo));
        $this->assertFalse(isset($container->unk));
    }

    /**
     * @dataProvider providerGet
     * @param string $key
     * @param boolean $serv
     * @param mixed $expected
     */
    public function testGet($key, $serv, $expected)
    {
        $container = new TContainer();
        $service = $container->$key;
        if ($serv) {
            $this->assertInstanceOf('axy\patterns\tests\tst\Service', $service);
            if (isset($expected[0]) && ($expected[0] === 'this')) {
                $expected[0] = $container;
            }
            $this->assertEquals($expected, $service->args);
            $this->assertSame($service, $container->$key);
        } else {
            $this->assertSame($expected, $service);
        }
    }

    /**
     * @return array
     */
    public function providerGet()
    {

        return [
            [
                'one',
                true,
                ['this', 1],
            ],
            [
                'two',
                true,
                ['this', 1, 'arg'],
            ],
            [
                'three',
                false,
                3,
            ],
            [
                'five',
                true,
                ['o'],
            ],
            [
                'mo',
                false,
                'mo',
            ],
        ];
    }

    /**
     * covers ::__get
     * @expectedException \axy\patterns\errors\ServiceNotExists
     * @expectedExceptionMessage Service "unk" is not exist in "Container"
     */
    public function testNotExists()
    {
        $container = new TContainer();
        return $container->unk;
    }

    /**
     * covers ::__get
     * @expectedException \axy\creator\errors\InvalidPointer
     */
    public function testInvalidPointer()
    {
        $container = new TContainer();
        return $container->four;
    }

    /**
     * covers ::__set
     * @expectedException \axy\magic\errors\ContainerReadOnly
     */
    public function testReadOnly()
    {
        $container = new TContainer();
        $container->one = 1;
    }

    public function testArrayAccess()
    {
        $container = new TContainer();
        $this->assertTrue(isset($container['one']));
        $this->assertFalse(isset($container['unk']));
        $this->assertSame('mo', $container['mo']);
    }

    /**
     * @expectedException \axy\errors\RequiresOverride
     * @return \axy\patterns\tests\tst\ContainerNoContext
     */
    public function testNoContext()
    {
        return new ContainerNoContext();
    }
}
