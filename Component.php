<?php

/*
 * This file is part of the dayax package.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core;

use dayax\core\EventHandler;

/**
 * Component Class.
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
abstract class Component
{
    /**
     * @var \dayax\core\EventHandler
     */
    private $eventHandler;

    /**
     * Get event handler object
     * @return \dayax\core\EventHandler
     */
    public function getEventHandler()
    {
        if(!is_object($this->eventHandler)){
            $this->eventHandler = new EventHandler();
        }
        return $this->eventHandler;
    }

    /**
     * Attach an event handler to this object
     *
     * @param string    $name Event name
     * @param callback  $handler A callable callback
     * @param number    $priority Handler priority
     * @see \dayax\core\EventHandler
     */
    public function addHandler($name,$handler,$priority=null)
    {
        $this->getEventHandler()->add($name, $handler,$priority);
    }

    /**
     * Check if property can be get
     * @param   string $name Property Name
     * @return  bool True if property can be get
     */
    public function canGetProperty($name)
    {
        return method_exists($this, 'get'.$name);
    }

    /**
     * Check if component has property
     * @param   string  $name The name property to check
     * @return  true    If this component have property
     */
    public function hasProperty($name)
    {
        $setter = 'set'.$name;
        $getter = 'get'.$name;
        return method_exists($this,$setter) || method_exists($this,$getter);
    }

    /**
     * Handle get function
     * @param   string  $name
     * @return  mixed
     * @throws  \dayax\core\InvalidOperationException
     */
    public function __get($name)
    {
        $getter = 'get'.$name;

        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (strncasecmp($name,'on',2)===0) {
            if (!method_exists($this,$name)) {
                throw new InvalidOperationException('core.event_undefined',get_class($this),$name);
            }
            $name=strtolower($name);
            if (!$this->getEventHandler()->hasEvent($name)) {
                return array();
            }
            return $this->getEventHandler()->getHandlers($name);
        }
        throw new InvalidOperationException('core.property_undefined',get_class($this),$name);
    }

    /**
     * Handle set function
     * @param   string  $name
     * @return  mixed
     * @throws  \dayax\core\InvalidOperationException
     */
    public function __set($name,$value)
    {
        $setter = 'set'.$name;
        $getter = 'get'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (strncasecmp($name,'on',2)===0) {
            $name = strtolower($name);
            $this->addHandler($name,$value);
        } elseif (method_exists($this, $getter)) {
            throw new InvalidOperationException('core.property_read_only',get_class($this),$name);
        } else {
            throw new InvalidOperationException('core.property_undefined',get_class($this),$name);
        }
    }

}