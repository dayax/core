<?php

/*
 * This file is part of the dayax project.
 *
 * (c) Anthonius Munthi <toni.dayax@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core;

use dayax\component\EventHandler;

/**
 * Component Class.
 *
 * @author Anthonius Munthi <toni.dayax@gmail.com>
 */
abstract class Component
{
    private $eventHandler;
    
    public function getEventHandler()
    {
        if(!is_object($this->eventHandler)){                
            $this->eventHandler = new EventHandler();
        }
        return $this->eventHandler;
    }
    
    public function __get($name)
    {
        $getter = 'get'.$name;

        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (strncasecmp($name,'on',2)===0) {
            if (!method_exists($this,$name)) {
                throw new InvalidOperationException('component.event_undefined',get_class($this),$name);
            }
            $name=strtolower($name);
            if (!isset($this->_handlers[$name])) {
                $this->_handlers[$name]=array();
            }

            return $this->_handlers[$name];
        }
        throw new InvalidOperationException('component.property_undefined',get_class($this),$name);
    }

    public function __set($name,$value)
    {
        $setter = 'set'.$name;
        $getter = 'get'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (strncasecmp($name,'on',2)===0) {
            $this->addHandler($name,$value);
        } elseif (method_exists($this, $getter)) {
            throw new InvalidOperationException('component.property_read_only',get_class($this),$name);
        } else {
            throw new InvalidOperationException('component.property_undefined',get_class($this),$name);
        }
    }
}