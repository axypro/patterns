<?php
/**
 * @package axy\patterns
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\patterns;

use axy\creator\Creator;
use axy\errors\RequiresOverride;
use axy\magic\LazyField;
use axy\magic\ArrayMagic;
use axy\magic\ReadOnly;
use axy\magic\Named;

/**
 * The container of subservices
 */
class Container implements \ArrayAccess
{
    use LazyField;
    use ArrayMagic;
    use ReadOnly;
    use Named;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->context = $this->getContextForCreator();
    }

    /**
     * Returns the context for creator
     *
     * @return array
     */
    protected function getContextForCreator()
    {
        if (!is_array($this->context)) {
            throw new RequiresOverride();
        }
        if (array_key_exists('arg_this', $this->context)) {
            if (isset($this->context['args']) && is_array($this->context['args'])) {
                array_unshift($this->context['args'], $this);
            } else {
                $this->context['args'] = [$this];
            }
            unset($this->context['arg_this']);
        }
        return $this->context;
    }

    /**
     * Returns the pointer for a subservice
     *
     * @param string $key
     * @return mixed
     */
    protected function getPointerForSub($key)
    {
        if (!is_array($this->subs)) {
            return null;
        }
        return isset($this->subs[$key]) ? $this->subs[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function magicCreateField($key)
    {
        $pointer = $this->getCachedPointer($key);
        if ($pointer === null) {
            $this->magicErrorFieldNotFound($key);
        }
        if (!$this->creator) {
            $this->creator = new Creator($this->context);
        }
        return $this->creator->create($pointer);
    }

    /**
     * {@inheritdoc}
     */
    protected function magicExistsField($key)
    {
        $pointer = $this->getCachedPointer($key);
        return ($pointer !== null);
    }

    /**
     * {@inheritdoc}
     */
    protected function magicErrorFieldNotFound($key)
    {
        throw new errors\ServiceNotExists($key, $this);
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getCachedPointer($key)
    {
        if (!array_key_exists($key, $this->cachePointers)) {
            $this->cachePointers[$key] = $this->getPointerForSub($key);
        }
        return $this->cachePointers[$key];
    }

    /**
     * The list of pointers for subservices
     * (for override)
     *
     * @var array
     */
    protected $subs;

    /**
     * The context for the creator of subservices
     *
     * @var array
     */
    protected $context;

    /**
     * Use $this as a first argument in a subservice constructor
     *
     * @var boolean
     */
    protected $argThis = false;

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'Container';

    /**
     * @var \axy\creator\Creator
     */
    protected $creator;

    /**
     * @var array
     */
    private $cachePointers = [];
}
