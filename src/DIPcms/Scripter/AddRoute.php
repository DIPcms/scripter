<?php

namespace DIPcms\Scripter;

use Nette;
use Nette\Application\Routers\Route;
use DIPcms\Scripter\Config;

class AddRoute extends Nette\Object{
	
	/**
	 * @param \Nette\Application\IRouter $router
	 * @param CliRouter $cliRouter
	 * @throws \Nette\Utils\AssertionException
	 * @return \Nette\Application\Routers\RouteList
	 */
	public static function prependTo(Nette\Application\IRouter &$router, self $cliRouter, Config $config){

                $_route = new Route($config->url_path_name . '/[<file_name>]/[<type>]', array(
                    'module'=> 'Scripter',
                    'presenter' => array(Route::VALUE => 'GetScript'),
                    'action' => 'default'
                ));
                
                
		$router[] = $_route;
		$lastKey = count($router) - 1;
		foreach ($router as $i => $route) {
			if ($i === $lastKey) {
				break;
			}
			$router[$i + 1] = $route;
		}
		$router[0] = $_route;
                return $router;
	}
}
