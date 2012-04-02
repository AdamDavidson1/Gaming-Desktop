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
 * Payload
 * 
 * Payload Utility to grab data from $_REQUEST for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */

/**
 * PayloadStackPkg Class
 * 
 * @package FrameD
 * @subpackage core
 */
class PayloadStackPkg{

   /**
    * Stack Data
    * 
    * @access private
    * @var mixed
    */
    private $stack;

/**
 * PayloadStackPkg Construct
 * 
 * @return void
 */
    function __construct(){
      //$this->stack = $data;
	  $this->stack = array();
    }

/**
 * Add Package to Stack
 * 
 * @param  PayloadPkg $pkg
 * @return void
 */
	public function addPackage($name, $pkg){
		$this->stack[$name] = $pkg;
	}

/**
 * Get Stack array data
 * 
 * @return array
 */
    public function getStack(){
		return $this->stack;
	}
}

/**
 * PayloadPkg Class
 * 
 * @package FrameD
 * @subpackage core
 */
class PayloadPkg{

   /**
    * Package Data
    * 
    * @access private
    * @var mixed
    */
    private $data;

   /**
    * Package Data From
    * 
    * @access private
    * @var string
    */
    private $from;
   
/**
 * PayloadPkg Construct
 * 
 * @param string $data Package data
 * @return void
 */
    function __construct($data, $from=NULL){
	  $this->data = $data;
	  $this->from = $from;
    }

/**
 * GetJSON
 * 
 * @access public
 * @return mixed
 */
    public function getJSON(){
	  return json_decode(stripslashes($this->data));
    }

/**
 * GetString
 * 
 * @access public
 * @return string
 */
    public function getString($from='ANY'){
	  if($from == 'ANY' || $this->from == $from){
	  	return stripslashes($this->data);
	  }
    }

/**
 * GetInt
 * 
 * @access public
 * @return int
 */
    public function getInt($default=0, $from='ANY'){
	   if($this->data+0 && ($from == 'ANY' || $this->from == $from)){
	      return $this->data;
	   } else {
	      return $default;
	   }
    }

/**
 * GetFloat
 * 
 * @access public
 * @return float
 */
    public function getFloat($default=0, $from='ANY'){
	   if($this->data+0 && ($from == 'ANY' || $this->from == $from)){
	      return $this->data;
	   } else {
	      return $default;
	   }
    }

/**
 * GetHash
 * 
 * @access public
 * @return array
 */
    public function getHash($delimiter=','){
	   if($this->data){
			   $data = explode($delimiter, $this->data);

			   if(strstr('=',$this->data)){
					   foreach($data as $item){
						  $parts = explode('=',$item);
						  $ret[$parts[0]] = $parts[1];
					   }
			   }else {
				$ret = $data;
			   }
			   unset($parts);
			   unset($data);
			   return $ret;
	   } else {
			   return false;
	   }
    }

/**
 * GetArray
 * 
 * @access public
 * @return array
 */
    public function getArray($from='ANY'){
	   return is_array($this->data) ? $this->data : array();
    }
}



/**
 * Payload Class
 * 
 * @package FrameD
 * @subpackage core
 */
class Payload {

/**
    * Payload Data
    * 
    * @access private
    * @var mixed
    */
	private $data;

/**
 * Payload Construct
 * 
 * @return void
 */
    function __construct(){
			$this->config = new Config();
			$this->logger = new Logger('Payload');

			foreach($_SERVER['argv'] as $index => $data){
				if(substr_count($data,'--')){
					$name = str_replace('--','',$data);
					$this->data[$name] = new PayloadPkg($_SERVER['argv'][($index+1)], 'CLI');
				}
			}

			foreach($_POST as $index => $data){
			   $this->data[$index] = new PayloadPkg($data, 'POST');
			}
			foreach($_GET as $index => $data){
			   $this->data[$index] = new PayloadPkg($data, 'GET');
			}

    }

/**
 * GetParam
 * 
 * @access public
 * @param  string $str Variable name
 * @return object PayloadPkg 
 */
	public function getParam($str){
		if($this->data[$str]){
		   return $this->data[$str];
	    } else {
		  return new PayloadPkg('');
		}
    }

/**
 * GetStack
 * 
 * @access public
 * @param  string $name Variable name
 * @return object PayloadStackPkg 
 */
    public function getStack($name){
		$keys = array_keys($this->data);
		$available = implode(',',$keys);

		preg_match_all('/'.$name.'[A-Za-z0-9\_]+/',$available,$matches);
		$pkg = new PayloadStackPkg();
		foreach($matches[0] as $index){
			$pkg->addPackage($index, $this->getParam($index));
		}

		return $pkg;
	}
}
?>
