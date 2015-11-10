<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\tests\tst;

use axy\patterns\Container;

class Service
{
    public $args;

    public function __construct()
    {
        $this->args = func_get_args();
    }
}
