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
 * Plugin Loader
 * 
 * Grabs the apropriate Plugin for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * PluginLoader Class
 * 
 * @package FrameD
 * @subpackage core
 */
class PluginLoader {

/**
 * PluginLoader Construct
 * 
 * @return void
 */
   function __construct(){

   }
/**
 * Load
 * 
 * Makes new Selected Plugin Object
 *
 * @access public
 * @return obj plugin object 
 */
   public function load($plugin){

	$this->logger = new Logger('PluginLoader');

	$config = new Config();

	$pluginFile = strtolower($plugin);
	$plugin = ucfirst(strtolower($plugin));

	if(!class_exists('Plugin')){
		require_once('core/Plugin.php');
	}

	if(is_file('app/plugins/'.$pluginFile.'_plugin.php')){

		require_once('app/plugins/'.$pluginFile.'_plugin.php');

	}elseif(is_file('core/app/plugins/'.$pluginFile.'_plugin.php')){

		require_once('core/app/plugins/'.$pluginFile.'_plugin.php');

	}else {
		$this->logger->error("Failed to Find $plugin in app/plugins/ and core/app/plugins/");
		$this->logger->error("Loading $plugin.");

		return;
	}
	if(!class_exists($plugin.'Plugin')){
		$this->logger->error("Loading $plugin.");

        return;
	}
	$class = $plugin.'Plugin';

	$pluginObj = new $class;

	return $pluginObj;
   }
}
?>
