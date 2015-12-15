<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManagerExtension
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace DIPcms\Scripter\DI;


use Nette;
use Nette\DI\CompilerExtension;

class ScripterExtension extends CompilerExtension{
    
   
    
    public function loadConfiguration() {
        
        $builder = $this->getContainerBuilder();
        
        $builder->addDefinition($this->prefix('config'))
		->setClass('DIPcms\Scripter\Config')
                ->setInject(false);
      
        
        $builder->addDefinition($this->prefix('cacheProvider'))
                ->setClass('DIPcms\Scripter\CacheProvider');
        
        $builder->addDefinition($this->prefix('latteFactory'))
		->setClass('DIPcms\Scripter\LatteFactory');
        
        
        $builder->addDefinition($this->prefix('scripter'))
		->setClass('DIPcms\Scripter\Scripter');
        
        
        $builder->addDefinition($this->prefix('macros'))
                ->setClass('DIPcms\Scripter\Macros',array(
                    $this->prefix('@config')
                ))
                ->setInject(FALSE);
        
        
    }
    
    
    
    public function afterCompile(Nette\PhpGenerator\ClassType $class){
        $initialize = $class->methods['initialize'];
        $initialize->addBody('$this->getService(?);', array($this->prefix('scripter')));
    }

    
    public function beforeCompile(){
        
        $builder = $this->getContainerBuilder();
        

        $builder->getDefinition('latte.templateFactory')
                ->addSetup('$service->setDefaultTemplateValue("__DIP_scripter_macros",?);', array(
                    $this->prefix('@macros')
                ));
        
        
        $builder->getDefinition('nette.latteFactory')
                ->addSetup('DIPcms\Scripter\Macros::addMacros($service,?);',array(
                            $this->prefix('@macros')
                ));
        
        $builder->getDefinition($this->prefix('latteFactory'))
                ->addSetup(
                        'DIPcms\Scripter\Macros::addMacrosForCompilerJsCSS($service,?);'.
                        '$service->addParams("_macros",?);'
                        ,array(
                            $this->prefix('@macros'),
                            $this->prefix('@macros')
                        )
                );
       
        
    }
    
    
    
    
    
    
    
     /**
     * @param \Nette\Configurator $configurator
     */
    public static function register(Nette\Configurator $configurator){
        
        $configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler){
                $compiler->addExtension('scripter', new ScripterExtension());
        };
    } 
    
  
}
