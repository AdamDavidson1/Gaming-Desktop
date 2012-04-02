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
 * Model Loader
 * 
 * Grabs the apropriate Model for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * ModelLoader Class
 * 
 * @package FrameD
 * @subpackage core
 */
class ModelLoader {

/**
 * ModelLoader Construct
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
   public function load($model, $db){

	$this->logger = new Logger('ModelLoader');

	$config = new Config();


	if(!$config->environment['DB'][$db]){
		$this->logger->error("Loading $model in $db Failed. Not in Config");

		return;
	}
	if(is_file('app/models/'.$db.'/'.$model.'_model.php') && is_file('app/models/'.$db.'/structure/'.$model.'_struct.php')){

		require_once('app/models/'.$db.'/'.$model.'_model.php');

	}elseif(is_file('core/app/models/'.$db.'/'.$model.'_model.php') && is_file('core/app/models/'.$db.'/structure/'.$model.'_struct.php')){

		require_once('core/app/models/'.$db.'/'.$model.'_model.php');

	}else {
		$this->logger->error("Loading $model in $db Failed. Not in Config");

		return;
	}
	$class = $this->makeModelName($model).'Model';

	$modelObj = new $class;

	return $modelObj;
   }

   private function makeModelName($widget){
        $parts = explode('_',$widget);

        foreach($parts as $index => $part){
                $parts[$index] = strtoupper(substr($part,0,1)).substr($part,1);
        }

        return implode("",$parts);
  }
}
?>
