<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DIPcms\Scripter;

use Nette;
use DIPcms\Scripter\Latte\LatteEngine;
use DIPcms\Scripter\Latte\LatteFactory;
use DIPcms\Scripter\Cache\CacheProvider;
use DIPcms\Scripter\Cache\CacheObject;

class Scripter{

    /**
     *
     * @var LatteEngine 
     */
    public $latte;

    /**
     *
     * @var CacheProvider
     */
    public $cache;

    /**
     *
     * @var Config
     */
    public $config;

   

    
    /**
     * 
     * @param \DIPcms\Scripter\CacheProvider $cache
     */
    public function __construct(
            CacheProvider $cache,
            LatteFactory $latte, 
            Config $config
    ){
        
        $this->cache = $cache;
        $this->config = $config;
        $this->latte = $latte;
    }

    
    /**
     * 
     * @param string $source
     * @param string $type_file
     * @return string
     */
    public function preparationSource($source, $type_file){

        switch($type_file){
            case "js": return $this->minifiJS($source);         
            case "css": return $this->minifiCss($source);
        }
        return $source;
        
    }
    

    /**
     * 
     * @param string $path
     * @throws \Exception
     * @return  CacheObject
     */
    public function addFile($path){
        
        $path = realpath($path);
        $file_cache = $this->cache->getFile($path);
        
        $file = new CacheObject($path);
        
        if($file->type == "css" || $file->type == "js"){
            $file = $this->latte->render($file, $this);
        }

        $file->source = $this->preparationSource($file->source, $file->type);
        
        if($file_cache){
            $this->cache->replaceFile($path, $file);
        }else{
            $this->cache->addFile($file);
        }
        
        return $file;
    }

    
    /**
     * 
     * @return array
     */
    public function getPrameters(){
        return $this->latte->getParams();
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function issetParameter($name){
        $parameters = $this->latte->getParams();  
        return isset($parameters[$name])? true : false; 
    }
    
    
    
    /**
     * 
     * @param string $name
     * @return mixin
     * @throws \Exception
     */
    public function __get($name){
        $parameters = $this->latte->getParams();  
        if(isset($parameters[$name])){
            return $parameters[$name];
        }
        throw new \Exception("Undefined property ". get_class($this)."::".$name);
    }
    
    /**
     * 
     * @param string $name
     * @param mixin $value
     */
    public function __set($name, $value) {
        $this->latte->addParams($name, $value);
    }
    
    
    /**
     * 
     * @return string
     */
    public function getPageName(){
        return $this->config->name;
    }
    
    
    /**
     * 
     * @param string $name
     * @param string $type
     * @return \DIPcms\Scripter\Source
     */
    public function getSource($name, $type){
        return new \DIPcms\Scripter\Source($this->cache->getCacheData(), $name, $type);
    }
    


    /**
     * 
     * @param string $content
     * @return string
     */
    public function minifiCss($content) {

        $content = preg_replace('/^\s*/m', '', $content);
        $content = preg_replace('/\s*$/m', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/\s*([\*$~^|]?+=|[{};,>~]|!important\b)\s*/', '$1', $content);
        $content = preg_replace('/([\[(:])\s+/', '$1', $content);
        $content = preg_replace('/\s+([\]\)])/', '$1', $content);
        $content = preg_replace('/\s+(:)(?![^\}]*\{)/', '$1', $content);
        $content = preg_replace('/\s*([+-])\s*(?=[^}]*{)/', '$1', $content);

        return trim($content);
    }
    
    /**
     * 
     * @param string $content
     * @return string
     */
    public function minifiJS($content){
        
        $content = str_replace(array("\r\n", "\r"), "", $content);
        $content = preg_replace('/[^\S\n]+/', ' ', $content);
        $content = str_replace(array(" \n", "\n "), "", $content);
        $content = preg_replace('/\n+/', "\n", $content);
        $content = preg_replace('/(?<![\+\-])\s*([\+\-])(?![\+\-])/', '\\1', $content);
        $content = preg_replace('/(?<![\+\-])([\+\-])\s*(?![\+\-])/', '\\1', $content);
        $content = preg_replace('/;+(?!\))/', ';', $content);
        $content = preg_replace('/(for\([^;]*;[^;]*;[^;\{]*\));(\}|$)/s', '\\1;;\\2', $content);

        return trim($content);
    }
    
    

}
