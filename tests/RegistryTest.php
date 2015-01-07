<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests;

use axy\patterns\Registry;

/**
 * coversDefaultClass axy\patterns\Registry
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::__construct
     * covers ::get
     * covers ::set
     * covers ::exists
     * covers ::remove
     */
    public function testPlain()
    {
        $registry = new Registry(['x' => 1, 'y' => 2]);
        $this->assertTrue($registry->exists('x'));
        $this->assertTrue($registry->exists('y'));
        $this->assertFalse($registry->exists('z'));
        $this->assertSame(1, $registry->get('x'));
        $this->assertSame(2, $registry->get('y'));
        $this->assertSame(null, $registry->get('z'));
        $this->assertSame('zz', $registry->get('z', 'zz'));
        $registry->set('x', 10);
        $this->assertTrue($registry->exists('x'));
        $this->assertSame(10, $registry->get('x'));
        $registry->remove('x');
        $this->assertFalse($registry->exists('x'));
        $this->assertSame(null, $registry->get('x'));
        $registry->set('z', 3);
        $this->assertTrue($registry->exists('z'));
        $this->assertSame(3, $registry->get('z'));
        $this->assertSame(3, $registry->get('z', 'zz'));
        $this->assertEquals(['y' => 2, 'z' => 3], $registry->getAllVars());
    }

    /**
     * covers ::__construct
     * covers ::get
     * covers ::set
     * covers ::exists
     * covers ::remove
     * covers ::isLoaded
     */
    public function testLazy()
    {
        $calls = 0;
        $creatorY = function ($key) use (&$calls) {
            $calls++;
            return 'c'.$key;
        };
        $registry = new Registry(['x' => 1], ['y' => $creatorY, 'z' => $creatorY]);
        $this->assertTrue($registry->exists('x'));
        $this->assertTrue($registry->exists('y'));
        $this->assertSame(1, $registry->get('x', 'def', false));
        $this->assertSame('def', $registry->get('y', 'def', false));
        $this->assertSame(0, $calls);
        $this->assertSame('cy', $registry->get('y', 'def'));
        $this->assertSame(1, $calls);
        $this->assertSame('cy', $registry->get('y', 'def'));
        $this->assertSame(1, $calls);
        $this->assertEquals(['x' => 1, 'y' => 'cy'], $registry->getAllVars(false));
        $this->assertTrue($registry->isLoaded('x'));
        $this->assertTrue($registry->isLoaded('y'));
        $this->assertFalse($registry->isLoaded('z'));
        $this->assertFalse($registry->isLoaded('a'));
        $this->assertEquals(['x' => 1, 'y' => 'cy', 'z' => 'cz'], $registry->getAllVars());
        $this->assertTrue($registry->isLoaded('z'));
        $this->assertFalse($registry->isLoaded('a'));
        $this->assertSame(2, $calls);
        $this->assertSame('cz', $registry->get('z', 'def'), false);
        $this->assertSame(2, $calls);
    }

    /**
     * covers ::__construct
     * covers ::get
     * covers ::set
     * covers ::setLazy
     * covers ::exists
     * covers ::remove
     * covers ::isConstant
     */
    public function testConstant()
    {
        $registry = new Registry(['x' => 1, 'y' => 2]);
        $this->assertFalse($registry->isConstant('x'));
        $this->assertFalse($registry->isConstant('y'));
        $this->assertFalse($registry->isConstant('z'));
        $registry->markAsConstant('y');
        $registry->set('z', 3, true);
        $this->assertEquals(['x' => 1, 'y' => 2, 'z' => 3], $registry->getAllVars());
        $this->assertFalse($registry->isConstant('x'));
        $this->assertTrue($registry->isConstant('y'));
        $this->assertTrue($registry->isConstant('z'));
        $registry->set('x', 10);
        $this->assertEquals(['x' => 10, 'y' => 2, 'z' => 3], $registry->getAllVars());
        $this->setExpectedException('axy\patterns\errors\PropertyReadOnly');
        $registry->set('y', 11);
    }

    /**
     * covers ::__construct
     * covers ::toReadOnly
     * covers ::isReadOnly
     */
    public function testToReadOnly()
    {
        $registry = new Registry(['x' => 1, 'y' => 2]);
        $registry->set('x', 5);
        $this->assertFalse($registry->isReadOnly());
        $this->assertTrue($registry->toReadOnly());
        $this->assertTrue($registry->isReadOnly());
        $this->assertFalse($registry->toReadOnly());
        $this->setExpectedException('axy\patterns\errors\ContainerReadOnly');
        $registry->set('x', 6);
    }

    /**
     * covers ::__construct
     * covers ::toReadOnly
     * covers ::isReadOnly
     */
    public function testContructReadOnly()
    {
        $registry = new Registry(['x' => 1, 'y' => 2], null, true);
        $this->assertTrue($registry->isReadOnly());
        $this->assertFalse($registry->toReadOnly());
        $this->setExpectedException('axy\patterns\errors\ContainerReadOnly');
        $registry->set('x', 6);
    }

    /**
     * covers ::__get
     * covers ::__set
     * covers ::__isset
     * covers ::__unset
     */
    public function testMagic()
    {
        $registry = new Registry();
        $registry->x = 1;
        $this->assertTrue(isset($registry->x));
        $this->assertFalse(isset($registry->y));
        $this->assertSame(1, $registry->x);
        $this->assertSame(null, $registry->y);
        $registry->x = 2;
        $registry->y = 3;
        $this->assertTrue(isset($registry->y));
        $this->assertEquals(['x' => 2, 'y' => 3], $registry->getAllVars());
        unset($registry->x);
        unset($registry->z);
        $this->assertFalse(isset($registry->x));
        $this->assertEquals(['y' => 3], $registry->getAllVars());
        $registry->toReadOnly();
        $this->setExpectedException('axy\patterns\errors\ContainerReadOnly');
        unset($registry->z);
    }

    public function testArrayAccess()
    {
        $registry = new Registry();
        $registry['x'] = 1;
        $this->assertTrue(isset($registry['x']));
        $this->assertFalse(isset($registry['y']));
        $this->assertSame(1, $registry['x']);
        $this->assertSame(null, $registry['y']);
        $registry['x'] = 2;
        $registry['y'] = 3;
        $this->assertTrue(isset($registry['y']));
        $this->assertEquals(['x' => 2, 'y' => 3], $registry->getAllVars());
        unset($registry['x']);
        unset($registry['z']);
        $this->assertFalse(isset($registry['x']));
        $this->assertEquals(['y' => 3], $registry->getAllVars());
        $registry->toReadOnly();
        $this->setExpectedException('axy\patterns\errors\ContainerReadOnly');
        unset($registry['z']);
    }

    public function testCountable()
    {
        $creatorY = function () {
            return 'y';
        };
        $registry = new Registry(['x' => 1], ['y' => $creatorY]);
        $this->assertCount(2, $registry);
        $registry->x = 1;
        $registry->y = 2;
        $registry->z = 3;
        $this->assertSame(2, $registry->y);
        $this->assertCount(3, $registry);
    }

    public function testIterator()
    {
        $creatorY = function () {
            return 'y';
        };
        $registry = new Registry(['x' => 1], ['y' => $creatorY]);
        $expected = [
            'x' => 1,
            'y' => 'y',
        ];
        $this->assertEquals($expected, \iterator_to_array($registry));
    }

    /**
     * covers ::exists
     */
    public function testExistsNull()
    {
        $registry = new Registry(['x' => null]);
        $this->assertTrue(isset($registry['x']));
        $this->assertSame(null, $registry->get('x', 'def'));
    }

    /**
     * covers ::setLazy
     * covers ::get
     */
    public function testNotCallable()
    {
        $registry = new Registry();
        $registry->setLazy('k', 'the_unknown_function_for_creating');
        $this->setExpectedException('axy\callbacks\errors\NotCallable');
        $registry->get('k');
    }

    /**
     * covers ::setVars
     */
    public function testSetVars()
    {
        $registry = new Registry();
        $vars = ['x' => 1, 'y' => 2];
        $lazy = ['y' => function ($key) {
            return $key.'!';
        }];
        $registry->setVars($vars, $lazy);
        $expected = [
            'x' => 1,
        ];
        $this->assertEquals($expected, $registry->getAllVars(false));
        $expected['y'] = 'y!';
        $this->assertEquals($expected, $registry->getAllVars(true));
    }
}
