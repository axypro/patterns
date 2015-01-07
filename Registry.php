<?php
/**
 * @package axy\patterns
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\patterns;

use axy\callbacks\Callback;
use axy\magic\ArrayMagic;
use axy\patterns\errors\ContainerReadOnly;
use axy\patterns\errors\PropertyReadOnly;

/**
 * The registry
 */
class Registry implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use ArrayMagic;

    /**
     * The constructor
     *
     * @param array $vars [optional]
     *        the list of variables (key => value)
     * @param array $lazy [optional]
     *        the list of lazy variables (key => creator)
     * @param boolean $readonly [optional]
     *        the read-only mode flag
     */
    public function __construct(array $vars = null, array $lazy = null, $readonly = false)
    {
        $this->vars = $vars ?: [];
        $this->lazy = $lazy ?: [];
        $this->readonly = $readonly;
    }

    /**
     * Sets a variable value
     *
     * @param string $name
     *        a variable name
     * @param mixed $value
     *        a variable value
     * @param boolean $const [optional]
     *        a variable read-only flag
     * @throws \axy\patterns\errors\ContainerReadOnly
     *         the registry is in read-only mode
     * @throws \axy\patterns\errors\PropertyReadOnly
     *         the variable is constant
     */
    public function set($name, $value, $const = false)
    {
        $this->checkWritable($name);
        $this->vars[$name] = $value;
        unset($this->lazy[$name]);
        $this->constants[$name] = $const;
    }

    /**
     * Sets a lazy variable
     *
     * @param string $name
     *        a variable name
     * @param callable $creator
     *        a creator of a variable value
     * @param boolean $const [optional]
     *        a variable read-only flag
     * @throws \axy\patterns\errors\ContainerReadOnly
     *         the registry is in read-only mode
     * @throws \axy\patterns\errors\PropertyReadOnly
     *         the variable is constant
     */
    public function setLazy($name, $creator, $const = false)
    {
        $this->checkWritable($name);
        $this->lazy[$name] = $creator;
        unset($this->vars[$name]);
        $this->constants[$name] = $const;
    }

    /**
     * Multi set of variables
     *
     * @param array $vars [optional]
     *        the list of variables (key => value)
     * @param array $lazy [optional]
     *        the list of lazy variables (key => creator)
     * @throws \axy\patterns\errors\ContainerReadOnly
     *         the registry is in read-only mode
     * @throws \axy\patterns\errors\PropertyReadOnly
     *         a variable is constant
     */
    public function setVars(array $vars = null, array $lazy = null)
    {
        if ($vars) {
            foreach ($vars as $k => $v) {
                $this->set($k, $v);
            }
        }
        if ($lazy) {
            foreach ($lazy as $k => $v) {
                $this->setLazy($k, $v);
            }
        }
    }

    /**
     * Returns a variable
     *
     * @param string $name
     *        a variable name
     * @param mixed $default [optional]
     *        a default value for a non-existing variable
     * @param boolean $load [optional]
     *        force load a lazy variable
     * @return mixed
     *         a value or default value
     * @throws \axy\callbacks\errors\NotCallable
     *         the creator for lazy variable is not callable
     */
    public function get($name, $default = null, $load = true)
    {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
        if ((array_key_exists($name, $this->lazy)) && $load) {
            $this->vars[$name] = Callback::call($this->lazy[$name], [$name]);
            unset($this->lazy[$name]);
            return $this->vars[$name];
        }
        return $default;
    }

    /**
     * Checks if a variable exists
     *
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        return (array_key_exists($name, $this->vars) || array_key_exists($name, $this->lazy));
    }

    /**
     * Removes a variable
     *
     * @param string $name
     *        the variable name
     * @return boolean
     *         TRUE - if the variable was indeed removed
     * @throws \axy\patterns\errors\ContainerReadOnly
     *         the registry is in read-only mode
     * @throws \axy\patterns\errors\PropertyReadOnly
     *         the variable is constant
     */
    public function remove($name)
    {
        $this->checkWritable($name);
        unset($this->vars[$name]);
        unset($this->lazy[$name]);
        $this->constants[$name] = false;
    }

    /**
     * Checks if a variable is constant
     *
     * @param string $name
     * @return boolean
     */
    public function isConstant($name)
    {
        return (!empty($this->constants[$name]));
    }

    /**
     * Checks if a variable has been loaded
     *
     * @param string $name
     * @return boolean
     */
    public function isLoaded($name)
    {
        return array_key_exists($name, $this->vars);
    }

    /**
     * Returns the list of all variables
     *
     * @param boolean $load [optional]
     *        FALSE - returns only loaded variables
     * @return array
     */
    public function getAllVars($load = true)
    {
        if ($load) {
            foreach ($this->lazy as $k => $v) {
                $this->get($k);
            }
        }
        return $this->vars;
    }

    /**
     * Marks a variable as constant
     *
     * @param string $name
     * @return boolean
     */
    public function markAsConstant($name)
    {
        if ($this->exists($name)) {
            $this->constants[$name] = true;
        }
    }

    /**
     * Switches the registry to read-only mode
     *
     * @return boolean
     *         switching occurred in a presently
     */
    public function toReadOnly()
    {
        if (!$this->readonly) {
            $this->readonly = true;
            return true;
        }
        return false;
    }

    /**
     * Checks if the registry mode is read-only
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->readonly;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($key)
    {
        return $this->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->vars) + count($this->lazy);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAllVars(true));
    }

    /**
     * @param string $name
     * @throws \axy\patterns\errors\ContainerReadOnly
     * @throws \axy\patterns\errors\PropertyReadOnly
     */
    private function checkWritable($name)
    {
        if ($this->readonly) {
            throw new ContainerReadOnly($this);
        }
        if (!empty($this->constants[$name])) {
            throw new PropertyReadOnly($this, $name);
        }
    }

    /**
     * @var array
     */
    private $vars;

    /**
     * @var array
     */
    private $lazy;

    /**
     * @var array
     */
    private $constants = [];

    /**
     * @var boolean
     */
    private $readonly;
}
