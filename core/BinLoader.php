<?php 
/*
 ______   ______     ______     __    __     ______     _____    
/\  ___\ /\  == \   /\  __ \   /\ "-./  \   /\  ___\   /\  __-.  
\ \  __\ \ \  __<   \ \  __ \  \ \ \-./\ \  \ \  __\   \ \ \/\ \ 
 \ \_\    \ \_\ \_\  \ \_\ \_\  \ \_\ \ \_\  \ \_____\  \ \____- 
  \/_/     \/_/ /_/   \/_/\/_/   \/_/  \/_/   \/_____/   \/____/ 
                                                                 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



/**
 * Bin Loader
 * 
 * Grabs the apropriate Bin for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * BinLoader Class
 * 
 * @package FrameD
 * @subpackage core
 */
class BinLoader {

/**
 * BinLoader Construct
 * 
 * @return void
 */
   function __construct(){

   }
/**
 * Load
 * 
 * Makes new Selected Model Object
 *
 * @access private
 * @return obj model object 
 */
   public function load($bin, $payload, $sessionData, $controller){

	$binFile = strtolower(str_replace('Bin', '',$bin));

	$this->logger = new Logger('BinLoader');

	$config = new Config();

	if(!class_exists('Bin')){
		require_once('core/Bin.php');
	}

	if(is_file('app/bins/'.$binFile.'.php')){

		require_once('app/bins/'.$binFile.'.php');

	}elseif(is_file('core/app/bins/'.$binFile.'.php')){

		require_once('core/app/bins/'.$binFile.'.php');

	}else {
		$this->logger->error("Loading $binFile Failed. No File Found");

		return;
	}
	$class = $bin;

	$binObj = new $class($bin, $payload, $sessionData, $controller);

	if(!method_exists($binObj, 'load')){
		$this->logger->error("Loading $bin Failed. Load Method Not Defined");

		return;
	}

	$method = new ReflectionMethod($binObj,'load');

    $params = $method->getParameters();
    $args = array();

	foreach($params as $param){
		try{
			$pkgClass     = $param->getClass();

			if($pkgClass){
				$pkgClass = $pkgClass->getName();
			}
		} catch(ReflectionException $e){
			preg_match('/Class\s([A-Za-z0-9]+)/', $e->getMessage(), $match);

			$pkgClass = $match[1];
		}

		if($pkgClass == 'PayloadPkg'){
			$args[] = $payload->getParam($param->name);
		}
		if($pkgClass == 'SessionDataPkg'){
			$args[] = $sessionData->getPkg($param->name);
		}
		if($pkgClass == 'PayloadStackPkg'){
			$args[] = $payload->getStack($param->name);
		}
		if(strstr($pkgClass,'Bin')){
			if((!$binLoader) || (!class_exists('BinLoader'))){
					/**
					 * FrameD BinLoader
					 */
					require_once('core/BinLoader.php');
					$binLoader = new BinLoader();
			}
			$args[] = $binLoader->load($pkgClass, $payload, $bundle->sessionData);
		}
	}

    $method->invokeArgs($binObj,$args);

	return $binObj;
   }
}
?>
