<?php
/**
 * @package axy\patterns
 */

namespace axy\patterns\errors;

/**
 * A subservice is not exists
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ServiceNotExists extends \axy\magic\errors\FieldNotExist
{
    protected $defaultMessage = 'Service "{{ key }}" is not exist in "{{ container }}"';
}
