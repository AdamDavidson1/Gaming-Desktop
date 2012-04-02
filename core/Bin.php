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
 * Bin
 * 
 * Basic Bin for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * Bin Class
 * 
 * @package FrameD
 * @subpackage core
 */
abstract class Bin {

/**
 * Logger Obj
 *
 * @access public
 */
   public $logger;

/**
 * Config Obj
 *
 * @access public
 */
   public $config;

/**
 * Plugin Loader Obj
 *
 * @access public
 */
   public $pluginLoader;

/**
 * Model Loader Obj
 *
 * @access public
 */
   public $modelLoader;

/**
 * Session Data Obj
 *
 * @access public
 */
   public $sessionData;

/**
 * Payload Obj
 *
 * @access public
 */
   public $payload;

/**
 * Controller Obj
 *
 * @access public
 */
   public $controller;

/**
 * Bin Construct
 * 
 * @return void
 */
   function __construct($binName, $payload, $sessionData, $controller){

	  $this->binName = $binName;
	
	  $this->config = new Config();

      $this->logger = new Logger($binName);

	  $this->pluginLoader = new PluginLoader();

	  $this->modelLoader = new ModelLoader();

	  $this->sessionData = $sessionData;

	  $this->payload = $payload;

	  $this->controller = $controller;
   }
 }
?>
