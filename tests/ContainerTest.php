<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests;

use axy\patterns\tests\nstst\Cont;

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
        $container = new Cont();
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
        $container = new Cont();
        $service = $container->$key;
        if ($serv) {
            $this->assertInstanceOf('axy\patterns\tests\nstst\Serv', $service);
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
     * @expectedExceptionMessage Service "unk" is not exist in "Cont"
     */
    public function testNotExists()
    {
        $container = new Cont();
        return $container->unk;
    }

    /**
     * covers ::__get
     * @expectedException \axy\creator\errors\InvalidPointer
     */
    public function testInvalidPointer()
    {
        $container = new Cont();
        return $container->four;
    }

    /**
     * covers ::__set
     * @expectedException \axy\magic\errors\ContainerReadOnly
     */
    public function testReadOnly()
    {
        $container = new Cont();
        $container->one = 1;
    }

    public function testArrayAccess()
    {
        $container = new Cont();
        $this->assertTrue(isset($container['one']));
        $this->assertFalse(isset($container['unk']));
        $this->assertSame('mo', $container['mo']);
    }
}
