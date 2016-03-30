<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DIPcms\Scripter;

use DIPcms\Scripter\Cache\CacheObject;

class Source {
    
    /**
     *
     * @var ArrayObject[][]|CacheObject 
     */
    private $cache_data = array();
    
    /**
     *
     * @var string 
     */
    private $name;
    
    /**
     *
     * @var string 
     */
    private $type;
    
    /**
     *
     * @var boolean 
     */
    private $type_file = true;
    
    /**
     *
     * @var ArrayObject[]|CacheObject 
     */
    public $data;
    
    
    
    /**
     * 
     * @param array $cache_data
     * @param string $name
     * @param string $type
     */
    public function __construct($cache_data, $name, $type) {
        
        $this->cache_data = $cache_data;
        $this->name = $name;
        $this->type = $type;
        
        if($type == "js" || $type == "css"){
            $this->type_file = false;
            $this->data = $this->searchSources();
        }else{
            $this->data = $this->searchFile();
        }
        
        
    }
    
    
    /**
     * 
     * @return ArrayObject[]|CacheObject
     */
    private function searchSources(){
        $result = array();
        foreach ($this->cache_data as $page_name => $page){
            if($page_name == $this->name){
                foreach($page as $file){
                    if($file->type == $this->type){
                        $result[] = $file;
                    }
                }
            }
        }
        return $result;
    }
    
    
    /**
     * 
     * @return null|CacheObject
     */
    private function searchFile(){
        foreach ($this->cache_data as $page_name => $page){
            foreach($page as  $file){
                
                if($file->name == $this->name){
                    return $file;
                }
            }
        }
        return null;
    }
    
    
    
    /**
     * @return boolean
     */
    public function isFile(){
        return $this->type_file;
    }
    
    /**
     * 
     * @return ArrayObject[]|CacheObject
     */
    public function getData(){
        return $this->data;
    }
    
    /**
     * 
     * @return string
     */
    public function getSource(){
        if($this->isFile()){
            return null;
        }
        $source = "";
        foreach($this->data as $data){
            $source .= $data->source? $data->source: "";
        }
        return $source;
    }
    
    /**
     * @return ArrayObject[][]|CacheObject
     */
    public function getCahceDate(){
        return $this->cache_data;
    }
    
}
