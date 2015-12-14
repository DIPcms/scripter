<?php


/**
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace DIPcms\Scripter;

use Nette;


class CacheObject extends Nette\Object{
    
   
    /**
     *
     * @var string 
     */
    public $name;
    
    
    /**
     *
     * @var string 
     */
    public $type;
    
    
    
    /**
     *
     * @var string 
     */
    public $file_name;
    
    
    /**
     *
     * @var string 
     */
    public $path;
    
    /**
     *
     * @var string
     */
    public $dir;
    
    
    
    /**
     *
     * @var integer
     */
    public $modefy;
    
    
    /**
     *
     * @var string 
     */
    public $source;
    
    /**
     *
     * @var array
     */
    public $files = array();
    
    
    
    public function __construct($path, $source = null){
       
        if(!file_exists($path)){
            throw new \Exception("$path file Not Found");
        }
        
        $this->name = md5($this->path);
        $file = new \SplFileInfo($path); 
        $this->type = $file->getExtension();
        $this->file_name = $file->getFilename();
        $this->path = $path;
        $this->dir = dirname($path);
        $this->modefy = $file->getMTime();
        
                
        $this->source = $source;
    }
    
    
    
}
