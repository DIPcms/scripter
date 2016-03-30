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
    public $base_path;
    
    
    /**
     *
     * @var string 
     */
    public $temp_dir;
    

    public function __construct($parameters) {
        
        $this->temp_dir = $parameters['tempDir'].'/cache/scripter';
        $this->name = md5($_SERVER['REQUEST_URI']);

    }
    
    
}
