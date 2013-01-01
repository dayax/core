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

/**
 * EventHandler Class
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class EventHandler
{
    /**
     * Store event handler
     * @var array
     */
    private $h = array();

    /**
     * Store the priority map for each event handler
     * @var array
     */
    private $map = array();

    /**
     * Store the priority count to be used
     * in a null priority handler
     * @var array
     */
    private $prioCount = array();

    /**
     * Attach an event handler
     * @param   string      $name       Event name
     * @param   callback    $handler    A valid callback to handle the event
     * @param   number      $priority   Handler priority
     * @throws  InvalidArgumentException If $handler is not callable
     */
    public function add($name,$handler,$priority=null)
    {
        $name = strtolower($name);
        if(!is_callable($handler)){
            throw new InvalidArgumentException('core.event_handler_uncallable');
        }
        $this->h[$name][] = $handler;
        if(is_null($priority)){
            if(!isset($this->prioCount[$name])){
                $this->prioCount[$name] = 1000;
            }
            $this->prioCount[$name]+=1;
            $priority = $this->prioCount;
        }
        $this->map[$name][] = array($handler,$priority);
        $this->sortHandler($name);
    }

    /**
     * Check if event exist
     * @param   string  $name
     * @return  boolean True if event exist
     */
    public function hasEvent($name)
    {
        $h = $this->h;
        if(!isset($h[$name])){
            return false;
        }
        return count($h[$name]) > 0 ? true:false;
    }

    /**
     * Sort event handler by priority
     * @param   string $name
     * @return  void
     */
    private function sortHandler($name)
    {
        //$map = $this->map[$name];
        usort($this->map[$name],function($a,$b){
            if(is_null($a[1])) $a[1] = 1000;
            if(is_null($b[1])) $b[1] = 1000;
            if($a[1]===$b[1]){
                return 0;
            }
            return ($a[1] <= $b[1]) ? -1 : +1;
        });

        $this->h[$name] = array();
        foreach($this->map[$name] as $index=>$data){
            $this->h[$name][] = $data[0];
        }

    }

    /**
     * Get all event handlers for given name
     * @param   string $name A name for event
     * @return  void
     */
    public function getHandlers($name)
    {
        return $this->h[$name];
    }

    /**
     * Raise event for the given name.
     *
     * @param   string  $name
     * @param   array   $parameters
     * @return  boolean
     */
    public function raiseEvent($name,Array $parameters=array())
    {
        if(!$this->hasEvent($name)){
            return false;
        }

        foreach($this->h[$name] as $handler){
            call_user_func_array($handler, $parameters);
        }
    }
}