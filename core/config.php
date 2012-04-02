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
 * Config
 * 
 * Config options loader for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */

/**
 * FrameD ConfigLoader
 */
require_once('core/ConfigLoader.php');

/**
 * Config Class
 * 
 * @package FrameD
 * @subpackage core
 */
final class Config{

   public $environment;

   public $application;

   private $declared;

   function __construct(){
		$this->declared = false;
		$configLoader = new ConfigLoader();

		$default_environment = $configLoader->load('default_environment');
		$default_application = $configLoader->load('default_application');

		$this->environment = array_merge($default_environment, $configLoader->load('environment'));
		$this->application = array_merge($default_application, $configLoader->load('application'));
   }
}
?>
