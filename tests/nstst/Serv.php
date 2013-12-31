<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\nstst;

class Serv extends \axy\patterns\Container
{
    public $args;

    public function __construct()
    {
        $this->args = \func_get_args();
    }
}
