<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DIPcms\Scripter;

use Nette;
use Nette\Http\Response;
use Nette\Application\UI\Form;
use DIPcms\Scripter\Scripter;

class GetScriptPresenter extends Nette\Application\UI\Presenter{
    
    /**
     *
     * @var Response @inject
     */
    public $response;
    
    /**
     *
     * @var Scripter @inject
     */
    public $scripter;
    

    public function __construct() {
        \Tracy\Debugger::$productionMode = TRUE;
    }
  
    
    public function renderDefault($file_name, $type){
        
        $response = $this->getHttpResponse();
        $source = $this->scripter->getSource($file_name, $type);

      
        if($source->isFile()){
            $data = $source->getData();
            if($data){
                
                $response->setContentType(mime_content_type($data->path), 'UTF-8');
                $response->addHeader("Content-Disposition", 'inline; filename="'.$data->file_name.'"');
                readfile($data->path);
                exit;
            }
        }else{
            $response->setContentType('text/'.$type, 'UTF-8');
            echo $source->getSource();
            exit;
            
        }
        //$response->setContentType(\Nette\Http\IResponse::S400_BAD_REQUEST, 'UTF-8');
        //exit;
        
    }
    
   
     
    
}