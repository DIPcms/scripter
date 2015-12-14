<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Latte
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace DIPcms\Scripter;

use Nette;
use Latte\Engine;
use DIPcms\Scripter\CacheProvider;

class LatteFactory extends Nette\Object{
    
    
    
    /**
     *
     * @var \Latte\Engine 
     */
    private $latte;
    
    
    
    /**
     *
     * @var CacheProvider
     */
    private $cache;
    
    
    
    /**
     *
     * @var array
     */
    private $params = array();
    
    
    public function __construct(CacheProvider $cache){
        $this->cache = $cache;
        $this->latte = new Engine();
        $parser = $this->latte->getParser();
        $parser->defaultSyntax = 'asp';
    }
    
    
    
    /**
     * 
     * @return Engine\Compiler
     */
    public function getCompiler(){
        return $this->latte->getCompiler();
    }
    
    
    
    /**
     * 
     * @param string $name
     * @param mixin $param
     */
    public function addParams($name, $param){
        $this->params[$name] = $param;
    }
    
    
    
    /**
     * 
     * @return array
     */
    public function getParams(){
        return $this->params;
    }
    
    
    
    /**
     * 
     * @return CacheObject
     */
    public function render(CacheObject $file){
        
        $this->params['file_rendering'] = $file;
        $file->source = $this->latte->renderToString($file->path, $this->params);
        return $file;
    }
    
}
