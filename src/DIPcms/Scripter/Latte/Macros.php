<?php


/**
 * Description of Routing
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace DIPcms\Scripter\Latte;

use Nette;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Engine;
use DIPcms\Scripter\Config;
use DIPcms\Scripter\Scripter;
use DIPcms\Scripter\Latte\LatteFactory;
use DIPcms\Scripter\Cache\CacheObject;

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
        
        
        
        /**
         * 
         * @param \DIPcms\Scripter\LatteFactory $latte
         * @param self $macros
         */
        public static function addMacro(LatteFactory $latte, self $macros){

            $macroSet = new \Latte\Macros\MacroSet($latte->getCompiler());
            $macroSet->addMacro("img", array($macros, "createMacroImg"));
            $macroSet->addMacro("file", array($macros, "createMacroFile"));
            
        }
        
        
        
        
        /**
         * 
         * @param \Latte\MacroNode $node
         * @param \Latte\PhpWriter $writer
         * @return string
         */
        public function createMacroImg(MacroNode $node, PhpWriter $writer){
            return $writer->write('echo $_scripter_macros->getImgLink(%node.args, $_scripter, $_scripter_file_rendering)');
        }
        
        
        /**
         * 
         * @param \Latte\MacroNode $node
         * @param \Latte\PhpWriter $writer
         * @return string
         */
        public function createMacroFile(MacroNode $node, PhpWriter $writer){
            return $writer->write('echo $_scripter_macros->getFileLink(%node.args, $_scripter, $_scripter_file_rendering)');
        }
        
        
        
        
        /**
         * 
         * @param string $path
         * @param \DIPcms\Scripter\CacheObject $file
         * @return string
         * @throws \Exception
         */
        public function getImgLink($path, Scripter $scripter, CacheObject $file_rendering){
            return 'url("'.$this->getFileLink($path, $scripter, $file_rendering).'")';
        }
         
        
        /**
         * 
         * @param string $path
         * @param \DIPcms\Scripter\CacheObject $file
         * @return string
         * @throws \Exception
         */
        public function getFileLink($path, Scripter $scripter, CacheObject $file_rendering){
            
            $f = realpath($file_rendering->dir . $path);
            $file = $scripter->addFile($f);
            $name = $file->name;
            if(!file_exists($f)){
                throw new \Exception($f.' file Not Found');
            }
            return '/'.$this->config->url_path_name.'/'.$name.'/'.$file->type;
        }
}

