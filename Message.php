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

/**
 * Message Class.
 *
 * @author Anthonius Munthi <toni.dayax@gmail.com>
 */
class Message extends Component
{
    private $cacheDir = null;

    private $cached = array();

    private $lang =  'en';
    
    private $catalogue = array();
    
    private $checksum = array();

    static private $instance = null;    
    
    public function __construct()
    {
        $this->addCatalogue('dayax', __DIR__.'/resources/messages');
    }

    public function setLanguage($lang)
    {
        $this->lang = substr($lang, 0,2);
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->lang;
    }
    
    public function setCacheDir($dir)
    {
        if(!is_dir($dir)){
            throw new InvalidArgumentException('core.message_cache_dir_invalid', $dir);
        }
        $this->cacheDir = $dir;
    }
    
    public function getCacheDir()
    {
        return $this->cacheDir;
    }
    
    public function translateMessage($what,$lang=null)
    {
        $dbg = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        
        $caller = $dbg[1];        
        if(isset($caller['class'])){
           $r = new \ReflectionClass($caller['class']);           
           $exp = explode('\\',$r->getNamespaceName());
           $namespace = $exp[0];              
        }
        
        $lang = is_null($lang) ? $this->lang:$lang;
        $cached = $this->cached;
        $trans = array();
        if(isset($cached[$namespace])){
            if(isset($cached[$namespace][$lang])){
                $trans = $cached[$namespace][$lang];
            }
        }
        $defaultLang = 'en';
        $defTrans = array();
        if(isset($cached[$namespace])){
            $defTrans = $cached[$namespace][$defaultLang];
        }
        
        $params = func_get_args();
        if (is_array($params[0])) {
            $params = $params[0];
        }
        
        $key = array_shift($params);
        $message = $key;
        if(isset($trans[$key])){
            $message = $trans[$key];
        }elseif(isset($defTrans[$key])){
            $message = $defTrans[$key];
        }
        
        $tokens = array();
        for ($i=0;$i<count($params);$i++) {
            $tokens["{".$i."}"] = $params[$i];
        }
        
        return strtr($message,$tokens);
    }
    
    public function addCatalogue($namespace,$dir)
    {
        if(!is_dir($dir)){
            throw new InvalidArgumentException('core.message_catalog_dir_invalid',$namespace,$dir);
        }
        $dir = realpath($dir);
        if(isset($this->catalogue[$namespace]) && $this->catalogue[$namespace]===$dir){
            return;
        }
        $this->catalogue[$namespace] = $dir;      
        $this->initCatalogue($namespace);
    }
    
    public function hasCatalogue($name)
    {
        return isset($this->catalogue[$name]) ? true:false;
    }
    
    private function initCatalogue($namespace)
    {
        $dir = $this->catalogue[$namespace];
        $files = array();       
        foreach(scandir($dir) as $file){
            if($file==='.' || $file==='..') continue;
            $files[] = $dir.DIRECTORY_SEPARATOR.$file;
        }
        
        $cached = array();
        foreach($files as $file){
            $contents = @file($file);
            $lang = strtr(basename($file),array(
                '.txt'=>'',
                'messages'=>'',
                '.'=>'',
            ));
            if($lang===''){
                $lang = 'en';
            }
            $cached[$lang] = array();
            array_walk($contents, create_function('&$item', '$item=trim($item);'));            
            foreach($contents as $content){
                $exp = explode("=",$content);
                array_walk($exp, create_function('&$item', '$item=trim($item);'));
                list($key,$msg) = $exp;
                $cached[$lang][$key] = $msg;
            }//end foreach contents
            
            //calculate checksum foreach file
            $cname = md5($file);           
            $this->checksum[$namespace][$cname] = md5_file($file);            
        }//end foreach files
        
        if(!isset($this->cached[$namespace])){
            $this->cached[$namespace] = array();
        }
        $this->cached[$namespace] = array_merge($this->cached[$namespace],$cached);                
    }
    
    /**
     * @return \dayax\core\Message
     */
    static public function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
}
