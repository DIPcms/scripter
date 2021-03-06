<?php


/**
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace DIPcms\Scripter\Cache;

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
     * @var string  
     */
    public $file_name_no_extension;
    
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
     * @var integer 
     */
    public $time_create;
    
    
    
    public function __construct($path, $source = null){
       
        if(!file_exists($path)){
            throw new \Exception("$path file Not Found");
        }
        
        $this->time_create = time();
        $this->name = session_id().'_'.md5($path);        
        $file = new \SplFileInfo($path); 
        
        $this->file_name_no_extension = str_replace('.'.$file->getExtension(), '', $file->getFilename());
        $this->type = $file->getExtension();
        $this->file_name = $file->getFilename();
        $this->path = $path;
        $this->dir = dirname($path);
        $this->modefy = $file->getMTime();
        
                
        $this->source = $source;
    }
    
    
    /**
     * 
     * @return \DateTime
     */
    public function getDateTimeCreate(){
        $date = new \DateTime();
        $date->setTimestamp($this->time_create);
        return $date;
    }
    
    
    
}
