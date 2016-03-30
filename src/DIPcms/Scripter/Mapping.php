<?php

namespace DIPcms\Scripter;

use Nette;
use Nette\Application\PresenterFactory;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Kdyby\Doctrine\Mapping\AnnotationDriver;
use Kdyby\DoctrineCache\Cache;
use Doctrine\Common\Annotations\CachedReader;


class Mapping extends Nette\Object{
    
    
    
     /**
     *
     * @var string 
     */
    public $maping_presenter = 'DIPcms\Scripter\*Presenter';
    
    /**
     * 
     * @param \Nette\Application\PresenterFactory $service
     * @param \DIP\FileManager\AddMaping $maping
     * @return \Nette\Application\PresenterFactory
     */
    public static function getPresenterMaping(PresenterFactory $service, Mapping $maping){
       
        
        if(method_exists($service, 'setMapping')){
            $service->setMapping(array(
                'Scripter' => $maping->maping_presenter
            ));
        }

        return $service;
    }

}
