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
 * Core
 * 
 * Simple requires for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */
date_default_timezone_set('America/Los_Angeles');

ini_set('display_errors',false);
ini_set('log_errors',1);
ini_set('error_log', 'logs/phperrors.log');

set_include_path('core/lib/pear/php/:../core/lib/pear/php/:../:./');


/**
 * FrameD Config
 */
require_once('core/config.php');

/**
 * FrameD Logger
 */
require_once('core/logger.php');

/**
 * FrameD Database Loader
 */
require_once('core/DbLoader.php');

/**
 * FrameD Model
 */
require_once('core/Model.php');

/**
 * FrameD Model
 */
require_once('core/Model.php');

/**
 * FrameD Payload
 */
require_once('core/payload.php');

/**
 * FrameD SessionData
 */
require_once('core/SessionData.php');

/**
 * FrameD Controller
 */
require_once('core/Controller.php');

/**
 * FrameD ModelLoader
 */
require_once('core/ModelLoader.php');

/**
 * FrameD PluginLoader
 */
require_once('core/PluginLoader.php');

 /**
  * Get File Name
  *
  * @ignore
  */
function get_current(){
	$path = explode('/',$_SERVER['SCRIPT_FILENAME']);
	return $path[(count($path)-1)];
}

if(!function_exists('get_called_class')) {
function get_called_class() {
    $matches=array();
    $bt = debug_backtrace();
    $l = 0;
    do {
        $l++;
        if(isset($bt[$l]['class']) AND !empty($bt[$l]['class'])) {
            return $bt[$l]['class'];
        }
        $lines = file($bt[$l]['file']);
        $callerLine = $lines[$bt[$l]['line']-1];
        preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                   $callerLine,
                   $matches);
        if (!isset($matches[1])) $matches[1]=NULL; //for notices
        if ($matches[1] == 'self') {
               $line = $bt[$l]['line']-1;
               while ($line > 0 && strpos($lines[$line], 'class') === false) {
                   $line--;                 
               }
               preg_match('/class[\s]+(.+?)[\s]+/si', $lines[$line], $matches);
       }
    }
    while ($matches[1] == 'parent'  && $matches[1]);
    return $matches[1];
}
}

 /**
  * loadFramework
  *
  * @ignore
  */
function loadFramework($cameFrom, $controller, $action, $format){
		$payload = new Payload();
		$config = new Config();
		$logger = new Logger('Core');

		if(is_file('app/controllers/'.$controller.'.php')){

			require_once('app/controllers/'.$controller.'.php');

			$logger->trace('LOADED CONTROLLER: '.$controller);

		} elseif(is_file('core/app/controllers/'.$controller.'.php')) {

			require_once('core/app/controllers/'.$controller.'.php');

			$logger->trace('LOADED CONTROLLER: core '.$controller);

		} else {
			$logger->trace('LOADED CONTROLLER: core error');
			require_once('core/app/controllers/error.php');
		}

		$class = strtoupper(substr($controller, 0,1)).substr($controller,1).'Controller';

		if(class_exists($class)){
			$bundle = new $class($format, $class, $cameFrom);

			$bundle->modelLoader = new ModelLoader();
			$bundle->pluginLoader = new PluginLoader();

		}else {
			if($config->environment['ERRORS']['404']){
					$logger->trace('CALLING ERROR 404 LAST CHANCE');
					require_once('core/app/controllers/error.php');

					$bundle = new ErrorController($format, $class, $cameFrom);

					$action = '404';

					callMethod($bundle, $cameFrom, '404', $payload);
			}else {
					header('HTTP/1.0 404 Not Found');
			}
			exit;
		}
		if(method_exists($bundle, $action)){

			callMethod($bundle,'', $action, $payload);
			return;

		} elseif(method_exists($bundle, $cameFrom.'_'.$action)){

			callMethod($bundle, $cameFrom, $action, $payload);
			return;

		} else {
				if(method_exists($bundle, 'preMethod')){
						$ret = $bundle->preMethod();

						if($ret){
							callMethod($bundle, $cameFrom, $action, $payload);
							return;
						}else {
							call404($bundle, $cameFrom, $format, $class, $payload);
						}
				}else {
					call404($bundle, $cameFrom, $format, $class, $payload);
				}
		}

}
function call404($bundle, $cameFrom, $format, $class, $payload){
		$config = new Config();

		if($config->environment['ERRORS']['404']){
						$logger->trace('CALLING ERROR 404 LAST CHANCE');
						require_once('core/app/controllers/error.php');

						$bundle = new ErrorController($format, $class, $cameFrom);

						$action = '404';

						callMethod($bundle, $cameFrom, '404', $payload);
		}else {
						header('HTTP/1.0 404 Not Found');
		}
		exit;
}
function callMethod($bundle, $cameFrom, $action, $payload){
			$bundle->actionName = $action;

			$ret = false;

			if(method_exists($bundle, 'preMethod')){
				$ret = $bundle->preMethod();
			}
			if($ret && method_exists($bundle, $ret)){
					$method = new ReflectionMethod($bundle,$ret);
			}elseif(!$cameFrom){
					$method = new ReflectionMethod($bundle,$action);
			}else {
					$method = new ReflectionMethod($bundle,$cameFrom.'_'.$action);
			}

			$params = $method->getParameters();
			$args = array();

			$methodName = $method->getName();

			foreach($params as $param){
				$logger = new Logger('callMethod');

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
					$args[] = $bundle->sessionData->getPkg($param->name);
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
					$args[] = $binLoader->load($pkgClass, $payload, $bundle->sessionData, $bundle);
				}
			}

			$logger = new Logger('Core CallMethod');

			$method->invokeArgs($bundle,$args);

			return;
}
chdir('..');
$config = new Config();
$logger = new Logger('Core');
$payload = new Payload();

date_default_timezone_set($config->environment['SERVER']['timezone']);

$format = $payload->getParam('format')->getString();

$action = $payload->getParam('action')->getString();

$controller = $payload->getParam('controller')->getString();

if($config->application['SERVER']['output_buffer'] == 'Compress'){
		ini_set('output_buffering',0);
		ini_set('output_handler','ob_gzhandler');
}
if($config->application['SERVER']['output_buffer'] == 'Off'){
		ini_set('output_buffering',0);
}else {
		ini_set('output_buffering',1);
}

$logger->trace('---------------------------------STARTING REQUEST-----------------------------------');
$logger->trace('URL REQUEST: '.($_SERVER["HTTPS"] == "on" ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
$logger->trace('FORMAT='.$format.' ACTION='.$action.' CONTROLLER='.$controller);

if(!$controller){

	$controller = $config->application['FRAMEWORK'][$cameFrom]['root']['controller'];
	$logger->trace('LOADING ROOT CONTROLLER: '.$controller);
}
if(!$action){

	$action = $config->application['FRAMEWORK'][$cameFrom]['default']['action'];
	$logger->trace('LOADING DEFAULT ACTION: '.$action);

}
?>
