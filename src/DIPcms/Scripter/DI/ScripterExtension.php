<?php


/**
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace DIPcms\Scripter\DI;


use Nette;
use Nette\DI\CompilerExtension;
use DIPcms\Scripter\Scripter;
use Nette\Http\Response;
use Nette\Http\Request;
ob_start("ob_gzhandler", 0, PHP_OUTPUT_HANDLER_REMOVABLE);


class ScripterExtension extends CompilerExtension{
    
  
    
    public function loadConfiguration() {
        
        $builder = $this->getContainerBuilder();
        
        $builder->addDefinition($this->prefix('config'))
		->setClass('DIPcms\Scripter\Config', array($builder->parameters))
                ->setInject(false);
      
        
        
        $builder->addDefinition($this->prefix('router'))
                ->setClass('DIPcms\Scripter\AddRoute')
                ->setAutowired(FALSE)
                ->setInject(FALSE);
        
        
        $builder->addDefinition($this->prefix('mapping'))
                ->setClass('DIPcms\Scripter\Mapping')
                ->setAutowired(FALSE)
                ->setInject(FALSE);
        
        
        $builder->addDefinition($this->prefix('cacheProvider'))
                ->setClass('DIPcms\Scripter\Cache\CacheProvider');
        
        $builder->addDefinition($this->prefix('latteFactory'))
		->setClass('DIPcms\Scripter\Latte\LatteFactory');

        
        
        

        
        $builder->addDefinition($this->prefix('scripter'))
		->setClass('DIPcms\Scripter\Scripter')
                ->addSetup('DIPcms\Scripter\DI\ScripterExtension::register_shutdown(?,?,?)',
                        array(
                            $this->prefix('@scripter'),
                            $builder->getDefinition('http.response'),
                            $builder->getDefinition('http.request')
                                
                ));
                
                
        
        $builder->addDefinition($this->prefix('macros'))
                ->setClass('DIPcms\Scripter\Latte\Macros',array(
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
        
        $builder->getDefinition($this->prefix('latteFactory'))
                ->addSetup(
                        '$macros = ?;'.
                        'DIPcms\Scripter\Latte\Macros::addMacro($service, $macros);'.
                        '$service->addParams("_scripter_macros", $macros);'
                        ,array(
                            $this->prefix('@macros'),
                        )
                );
        
        $builder->getDefinition($builder->getByType('Nette\Application\IRouter') ?: 'router')
            ->addSetup('$service = DIPcms\Scripter\AddRoute::prependTo($service, ?, ?)', array($this->prefix('@router'), $this->prefix('@config')));
        
        
        $builder->getDefinition($builder->getByType('Nette\Application\IPresenterFactory') ?: 'nette.presenterFactory')
                ->addSetup('$service = DIPcms\Scripter\Mapping::getPresenterMaping($service, ?)', array($this->prefix('@mapping')));
        
    }
    
    
    /**
     * 
     * @param Scripter $scripter
     * @param Response $response
     * @param Request $request
     */
    public static function register_shutdown(Scripter $scripter, Response $response, Request $request){
        register_shutdown_function(function()use($scripter, $response){
                        
            $page = ob_get_contents();
            ob_end_clean();

            $header_type = $response->getHeader("Content-Type");
                
            if($header_type && strpos($header_type, "text/html") === 0){
                preg_match('/(?:<head[^>]*>)(.*?)<\/head>/s', $page, $matches);
                if(isset($matches[1])){
                   $replace = $matches[1];
                   $matches[1] .= '<script type="text/javascript" src="'. '/'. $scripter->config->url_path_name .'/' .$scripter->getPageName() . '/js' .'"></script>';
                   $matches[1] .= '<link rel="stylesheet" href="'. '/'. $scripter->config->url_path_name .'/' .$scripter->getPageName() . '/css' .'">'; 
                   $page = str_replace($replace, $matches[1], $page);
                }
            }
            echo $page;
        });
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
