<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\tst;

use axy\magic\ArrayMagic;
use axy\patterns\Container;

class TContainer extends Container
{
    use ArrayMagic;

    /**
     * {@inheritdoc}
     */
    protected function getPointerForSub($key)
    {
        if ($key === 'mo') {
            return ['value' => 'mo'];
        }
        return parent::getPointerForSub($key);
    }

    /**
     * {@inheritdoc}
     */
    protected $context = [
        'namespace' => 'axy\patterns\tests',
        'args' => [1],
        'arg_this' => true,
    ];

    /**
     * {@inheritdoc}
     */
    protected $subs = [
        'one' => 'tst\Service',
        'two' => ['\axy\patterns\tests\tst\Service', ['arg']],
        'three' => [
            'value' => 3,
        ],
        'four' => 4,
        'five' => [
            'classname' => 'tst\Service',
            'options' => 'o',
            'reset_args' => true,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'Container';
}
