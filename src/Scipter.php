<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DIPcms\Scripter;

use Nette;
use MatthiasMullie\Minify;
use Latte;
use Nette\Caching\Storages\FileStorage;
use Nette\Caching\Cache;
use DIPcms\Scripter\LatteEngine;

class Scripter extends Nette\Object {

    /**
     *
     * @var LatteEngine 
     */
    private $latte;

    /**
     *
     * @var CacheProvider
     */
    private $cache;

    /**
     *
     * @var Config
     */
    private $config;

    
    
    /**
     * 
     * @param \DIPcms\Scripter\CacheProvider $cache
     */
    public function __construct(
            CacheProvider $cache,
            LatteFactory $latte, 
            Config $config
    ) {

        $this->cache = $cache;
        $this->config = $config;
        $this->latte = $latte;

        
        $files = $this->cache->getAllFiles();
        if(!$files || count($files) == 1 && count(end($files)) == 0){
            $this->claerScripterDir($this->config->base_path);
        }
        
        $this->createDirFile();
    }

    
    
    private function createDirFile() {
        if (!is_dir($this->config->base_path)) {
            mkdir($this->config->base_path, 0777);
        }

        if (!file_exists($this->config->file_path_js)) {
            fclose(fopen($this->config->file_path_js, "w"));
        }

        if (!file_exists($this->config->file_path_css)) {
            fclose(fopen($this->config->file_path_css, "w"));
        }
    }
    
    
    
    
    /**
     * 
     * @param string $directory
     */
    private function claerScripterDir($directory){
        
        foreach(glob("$directory/*") as $file){
            if(is_dir($file)) { 
                $this->claerScripterDir($file);
            } else {
                unlink($file);
            }
        }
    }
    

    /**
     * 
     * @param string $path
     */
    public function addCss($path) {

        $path = realpath($path);
        $file_cache = $this->cache->getFile($path);
                
        if($file_cache && $file_cache->modefy !== filemtime($path)) {
           $file = $this->latte->render(new CacheObject($path));
           $file->source = $this->minifiCss($file->source);
           $this->cache->replaceFile($path, $file);
           $this->writeFile();
           
        }elseif (!$file_cache) {
            $file = $this->latte->render(new CacheObject($path));
            $file->source = $this->minifiCss($file->source);
            $this->cache->addFile($file);
            $this->writeFile();
        }
        
        
    }

    
    
    /**
     * 
     * @param string $path
     */
    public function addJs($path) {

        $path = realpath($path);
        $file_cache = $this->cache->getFile($path);
        
        
        if ($file_cache && $file_cache->modefy !== filemtime($path)) {

            $css = file_get_contents($path);
            $file = new CacheObject($path, $this->minifiJS($css));
            $this->cache->replaceFile($path, $file);
            $this->saveFile('js');
            
        } elseif (!$file_cache) {

            $css = file_get_contents($path);
            $file = new CacheObject($path, $this->minifiJS($css));
            $this->cache->addFile($file);
            $this->saveFile('js');
        }
    }
    
    
    
    /**
     * write file
     * @param string $type
     */
    private function writeFile($type = 'css') {

        $source = "";
        foreach ($this->cache->getFiles() as $file) {
            if ($file->type == $type) {
                $source .= $file->source;
                
                foreach($file->files  as $index => $_copy_file){
                    
                    $name = $this->config->base_path.'/'.$_copy_file['name'];
                    
                    if($_copy_file['copy']){
                        
                        if($_copy_file['modefy'] !== filemtime($_copy_file['file'])){
                            unlink($name);
                            copy($_copy_file['file'], $name);
                            $file->files[$index]['copy'] = true;
                        }
                        
                    }else{
                        copy($_copy_file['file'], $name);
                        $file->files[$index]['copy'] = true;
                        
                    }
                    $this->cache->replaceFile($file->path, $file);
                }
            }
        }
        
        file_put_contents($type == 'css' ? $this->config->file_path_css : $this->config->file_path_js, $source);
    }

    

    /**
     * @return string
     */
    public function getCssPath() {
        return $this->config->file_path_css;
    }

    /**
     * 
     * @return string
     */
    public function getJsPath() {
        return $this->config->file_path_js;
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
        $content = preg_replace('/;(\}|$)/s', '\\1', $content);

        return trim($content);
    }
    
    

}
