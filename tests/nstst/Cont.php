<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\nstst;

class Cont extends \axy\patterns\Container
{
    use \axy\magic\ArrayMagic;

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
        'one' => 'nstst\Serv',
        'two' => ['\axy\patterns\tests\nstst\Serv', ['arg']],
        'three' => [
            'value' => 3,
        ],
        'four' => 4,
        'five' => [
            'classname' => 'nstst\Serv',
            'options' => 'o',
            'reset_args' => true,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'Cont';
}
