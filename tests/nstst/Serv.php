<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\nstst;

use axy\patterns\Container;

class Serv extends Container
{
    public $args;

    public function __construct()
    {
        $this->args = func_get_args();
    }
}
