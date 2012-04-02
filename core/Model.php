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
 * Model
 * 
 * Basic Db Model for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * Model Class
 * 
 * @package FrameD
 * @subpackage core
 */
abstract class Model {

/**
 * Database Obj
 *
 * @access public
 */
   public $db;

/**
 * Config Obj
 *
 * @access public
 */
   public $config;


/**
 * Model Construct
 * 
 * @return void
 */
   function __construct($tableName){

	  $this->tableName = $tableName;
	
	  $this->config = new Config();

      $this->logger = new Logger($this->tableName."Model");

	  $this->logger->debug($this->dbType);
   }

/**
 * SmartInsert
 *  
 * @access public
 * @param  array  $data value
 * @return int    new record ID
 */
  public function smartInsert($data){
	return $this->db->smartInsert($this->tableName, $data);
  }

/**
 * SmartUpdate
 *  
 * @access public
 * @param  array  $data value
 * @param  array  $where value
 * @return int    affected rows
 */
  public function smartUpdate($data,$where){
	return $this->db->smartUpdate($this->tableName, $data,$where);
  }

/**
 * SmartSelect
 *  
 * @access public
 * @param  array  $columns value
 * @param  array  $where value
 * @return array  db records
 */
  public function smartSelect($columns, $where){
	return $this->db->smartSelect($this->tableName, $columns, $where);
  }

/**
 * SmartSelectOne
 *  
 * @access public
 * @param  array  $columns value
 * @param  array  $where value
 * @return array  db records
 */
  public function smartSelectOne($columns, $where){
	return $this->db->smartSelectOne($this->tableName, $columns, $where);
  }
 }
?>
