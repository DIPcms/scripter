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
    public $url_path_name = "getsource";
     
    /**
     *
     * @var string 
     */
    public $default_syntax = "asp";
    
    
    /**
     *
     * @var string 
     */
    public $temp_dir;
    

    public function __construct($parameters) {
        
       
        foreach($parameters as $name=>$value){
            if(property_exists($this, $name)){
                $this->$name = $value;
            }
        }
        
        
        $this->name = session_id().'_'.md5($_SERVER['REQUEST_URI']);

    }
    
    
}
