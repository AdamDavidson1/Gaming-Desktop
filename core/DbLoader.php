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
 * Database
 * 
 * Simple SQLite3 DB Connect for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * DbLoader Class
 * 
 * @package FrameD
 * @subpackage core
 */
class DbLoader {

/**
 * DbLoader Construct
 * 
 * @return void
 */
   function __construct(){

   }
/**
 * Load
 * 
 * Makes new Database Object
 *
 * @access private
 * @return obj db object 
 */
   public function load($db){

	$this->logger = new Logger('DbLoader');

	$config = new Config();

	if(!$config->environment['DB'][$db]['type']){
		$this->logger->error('Loading '.$db.' Failed. Not in Config');

		return;
	}
	$type = $config->environment['DB'][$db]['type'].'Db';

	require_once('core/databases/'.$type.'.php');

	$this->db = new $type($db);

	return $this->db;
   }
}
?>
