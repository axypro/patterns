<?php
/**
 * Some basic patterns
 *
 * @package axy\patterns
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/patterns/master/LICENSE MIT
 * @link https://github.com/axypro/patterns repository
 * @link https://packagist.org/packages/axy/patterns on packagist.org
 * @uses PHP5.4+
 */

namespace axy\patterns;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
