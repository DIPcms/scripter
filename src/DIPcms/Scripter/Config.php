<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace DIPcms\Scripter;

use Nette;

class Config extends Nette\Object{
    
    
    /**
     *
     * @var string 
     */
    public $name;
    
    
    /**
     *
     * @var string
     */
    public $base_path;
    
    
    /**
     *
     * @var string
     */
    public $base_link = '/scripter';
    
    

    /**
     *
     * @var string
     */
    public $file_path_js;
    
    
    /**
     *
     * @var string 
     */
    public $file_path_css;
    
    
    /**
     *
     * @var string 
     */
    public $link_js;
    
    /**
     *
     * @var string
     */
    public $link_css;
    
    
    public function __construct() {
        
        $this->base_path = $_SERVER['DOCUMENT_ROOT'].'/scripter';
        $this->name = md5($_SERVER['REQUEST_URI']);
        
        $path = $this->base_path.'/'.$this->name;
        $this->file_path_css = $path.'.css';
        $this->file_path_js = $path.'.js';
        
        $link = $this->base_link.'/'.$this->name;
        $this->link_js = $link.'.js';
        $this->link_css = $link.'.css';
        
    }
    
    
}
