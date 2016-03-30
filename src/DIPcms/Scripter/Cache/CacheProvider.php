<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CacheProvider
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace DIPcms\Scripter\Cache;

use Nette;
use Nette\Caching\Cache;
use \DIPcms\Scripter\Config;

class CacheProvider extends Nette\Object{
    
    
    /**
     *
     * @var Config 
     */
    public $config;
    
    /**
     *
     * @var Cache
     */
    private $cache;
    
    
    /**
     *
     * @var string
     */
    private $cache_file_name;
    
    
    /**
     *
     * @var ArrayObject[]|\DIPcms\Scripter\Cache\CacheObject
     */
    private $cache_data;
    
    
    public function __construct(Config $config) {
        $this->config = $config;
        
        
        if (!is_dir($config->temp_dir)) {
            mkdir($config->temp_dir, 0777);
        }
        $storage = new Nette\Caching\Storages\FileStorage($config->temp_dir);
        $this->cache = new Cache($storage);
                
        $this->cache_file_name = $this->createCacheFileName();
        $this->cache_data = $this->getCache();

    }
    
    
    /**
     * 
     * @return ArrayObject[][]|\DIPcms\Scripter\CacheObject
     */
    private function getCache(){
        $source = $this->cache->load('scripter');
        $source = $source? $source : array();
        if(!isset($source[$this->cache_file_name])){
            $source[$this->cache_file_name] = array();
        }
        $this->cache->save('scripter', $source);
        return $source;
    }
    
    /**
     * 
     * @return ArrayObject[][]|\DIPcms\Scripter\CacheObject
     */
    public function getCacheData(){
        return $this->cache_data;
    }
    
    
    /**
     * 
     * @return string
     */
    public function createCacheFileName(){
        return md5($_SERVER['REQUEST_URI']);   
    }
    
    
    
    /**
     * @return string 
     */
    public function getCacheFileName(){
        return $this->cache_file_name;
    }
    
    
    
    /**
     * 
     * @param \DIPcms\Scripter\Cache\CacheObject $file
     */
    public function addFile(CacheObject $file){
        
        $this->cache_data[$this->cache_file_name][] = $file; 
        $this->cache->save('scripter', $this->cache_data);
        
    }
    
    
    /**
     * 
     * @return ArrayObject[][]|\DIPcms\Scripter\CacheObject
     */
    public function getAllFiles(){
        return $this->cache_data;
    }
    
    
    /**
     * 
     * @return ArrayObject[]|\DIPcms\Scripter\CacheObject
     */
    public function getFiles(){
        return $this->cache_data[$this->cache_file_name];
    }
    
    
    
    
    /**
     * 
     * @param string $path
     * @param \DIPcms\Scripter\CacheObject $toReplace
     * @throws \Exception
     */
    public function replaceFile($path, CacheObject $toReplace){
        $instaled = false;
        
        foreach($this->cache_data[$this->cache_file_name] as $index => $file){
            if($file->path == $path){
                $this->cache_data[$this->cache_file_name][$index] = $toReplace;
                $this->cache->save('scripter', $this->cache_data);
                $instaled = true;
            }
        }
        
        if(!$instaled){
            throw new \Exception($path." is not installed in the cache");
        }
        
    }
    
    
    
    /**
     * 
     * @return \DIPcms\Scripter\CacheObject
     */
    public function getFile($path){
        
        foreach($this->cache_data[$this->cache_file_name] as $file){
            if($file->path == $path){
                return $file;
            }
        }
        return null;
        
    }

    
    
}
