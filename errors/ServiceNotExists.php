<?php
/**
 * @package axy\patterns
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\patterns\errors;

use axy\magic\errors\FieldNotExist;

/**
 * A sub service is not exists
 */
class ServiceNotExists extends FieldNotExist
{
    protected $defaultMessage = 'Service "{{ key }}" is not exist in "{{ container }}"';
}
