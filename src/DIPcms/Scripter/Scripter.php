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

class Scripter extends Nette\Object {

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
        
        if(!exif_imagetype($path)){
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
     * @param string $name
     * @param mixin $value
     */
    public function addParam($name, $value){
        $this->latte->addParams($name, $value);
    }
    
    
    
    /**
     * 
     * @param string $type
     * @return string
     */
    public function getSourceString($type = null){
        $type = $type ? $type : $this->config->accept_file_type[0];
        return $this->cache->getDataByType($type);
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
     * @param md5 $page
     * @param string $type
     * @return string
     */
    public function getSourceStringByPage($page, $type){
        //dump($this->cache->getFiles());
        return $this->cache->getDataByPage($page, $type);
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
