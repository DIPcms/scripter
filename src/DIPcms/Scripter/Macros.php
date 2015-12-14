<?php


/**
 * Description of Routing
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace DIPcms\Scripter;

use Nette;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Engine;

class Macros extends Nette\Object{
        
        
        /**
         *
         * @var Config
         */
        public $config;
        
        
        
        public function __construct(
                Config $config
            ){
            $this->config = $config;
        }
        
        
        

	public static function addMacros(Engine $latte, self $macros){
           
            $macroSet = new \Latte\Macros\MacroSet($latte->getCompiler());
            $macroSet->addMacro('scripter', 'echo $__DIP_scripter_macros->getScripter()');

	}
        
        
        public function getScripter(){
            
            return '<script type="text/javascript" src="'.$this->config->link_js.'"></script>'
                   .'<link href="'.$this->config->link_css.'" rel="stylesheet">';
            
        }
        
        
        
        
        
        
        /****************************** Latte Engine css js*********************/
        
        
        /**
         *
         * @var array 
         */
        public $macros_list = array(
            'img' => 'createMacroImg',

        );
        
        
        
        
        
        /**
         * 
         * @param \DIPcms\Scripter\LatteFactory $latte
         * @param self $macros
         */
        public static function addMacrosForCompilerJsCSS(LatteFactory $latte, self $macros){
            $macroSet = new \Latte\Macros\MacroSet($latte->getCompiler());
            foreach($macros->macros_list as $name => $callback){
                $macroSet->addMacro($name, array($macros, $callback));
            }
        }
        
        
        
        
        /**
         * 
         * @param \Latte\MacroNode $node
         * @param \Latte\PhpWriter $writer
         * @return string
         */
        public function createMacroImg(MacroNode $node, PhpWriter $writer){
            return $writer->write('echo $_macros->getImgLink(%node.args, $file_rendering)');
        }
        
        
        
        
        
        
        /**
         * 
         * @param string $path
         * @param \DIPcms\Scripter\CacheObject $file
         * @return string
         * @throws \Exception
         */
        public function getImgLink($path, CacheObject $file){
            
            $name = $file->name.'_'.basename($path);
            
            if(!file_exists($file->dir.$path)){
                throw new \Exception($file->dir.$path.' file Not Found');
            }
            
            
            $include_file = realpath($file->dir.$path);
            
            $isset = false;
            foreach($file->files as $f){
                if($f['file'] == $include_file){
                    $isset = true;
                }
            }
            if(!$isset){
                $file->files[] = [
                   'file' => realpath($file->dir.$path),
                   'name' => $name,
                   'modefy' => filemtime($file->dir.$path),
                   'copy' => false
                ];
            }
            return 'url("'.$this->config->base_link.'/'.$name.'")';
        }
         
}

