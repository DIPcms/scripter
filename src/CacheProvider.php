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

namespace DIPcms\Scripter;

use Nette;
use Nette\Caching\Cache;

class CacheProvider extends Nette\Object{
    
    
    /**
     *
     * @var FileStorage 
     */
    private $storage;
    
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
     * @var ArrayObject[]|\DIPcms\Scripter\CacheObject
     */
    private $cache_data;
    
    
    public function __construct(Nette\Caching\IStorage $storage) {
        $this->storage = $storage;
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
        if(!$source || !isset($source[$this->cache_file_name])){
           $source = array($this->cache_file_name => array());
           $this->cache->save('scripter', $source);
        }
        return $source;
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
     * @param \DIPcms\Scripter\CacheObject $file
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
    public function replaceFile($path, \DIPcms\Scripter\CacheObject $toReplace){
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
