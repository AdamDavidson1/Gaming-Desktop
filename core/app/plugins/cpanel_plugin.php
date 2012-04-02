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
 * CpanelPlugin
 * 
 * Cpanel Plugin for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * Cpanel Plugin Class
 * 
 * @package FrameD
 * @subpackage core
 */


class CpanelPlugin extends Plugin{
	
/**
 * Config Package
 *
 * @access private
 */
	private $package;

/**
 * Config Port
 *
 * @access private
 */
	private $port;

/**
 * Config SSL
 *
 * @access private
 */
	private $ssl;

/**
 * Config Domain
 *
 * @access private
 */
	private $domain;

/**
 * Config User
 *
 * @access private
 */
	private $user;

/**
 * Config Password
 *
 * @access private
 */
	private $pass;


	function __construct(){
		parent::__construct();
	}


/**
 * Get Data
 * 
 * @access public
 * @return object
 */
	public function getDNSZones(){
		return $this->getData('listzones');
	}

    public function getZoneRecord($domain, $line=1){
		$ret = $this->getData('cpanel', array('user' => $this->user,
											  'cpanel_jsonapi_module' => 'SubDomain', 
											  'cpanel_jsonapi_func' => 'listsubdomains',
											  'cpanel_jsonapi_apiversion' => 2));
		/*stdClass Object
(
    [cpanelresult] => stdClass Object
        (
            [data] => Array
                (
                    [0] => stdClass Object
                        (
                            [domainkey] => airnotion_gatevo.com
                            [status] => not redirected
                            [reldir] => home:airnotion.com
                            [dir] => /home/darkken/airnotion.com
                            [subdomain] => airnotion
                            [rootdomain] => gatevo.com
                            [domain] => airnotion.gatevo.com
                            [basedir] => airnotion.com
                        )
*/
		foreach($ret->cpanelresult->data as $record){
			if($record->domain == $domain.'.'.$this->domain){
				return $record;
			}
		}
	}

	public function addSubdomain($domain){
		return $this->getData('cpanel', array('user' => $this->user,
									   'cpanel_jsonapi_module' => 'SubDomain',
									   'cpanel_jsonapi_func' => 'addsubdomain',
									   'cpanel_jsonapi_apiversion' => 2,
									   'dir' => $this->userdir,
									   'rootdomain' => $this->domain,
									   'domain' => $domain
									   ));
	}

	public function delSubdomain($domain){
		return $this->getData('cpanel', array('user' => $this->user,
                                       'cpanel_jsonapi_module' => 'SubDomain',
                                       'cpanel_jsonapi_func' => 'delsubdomain',
                                       'cpanel_jsonapi_apiversion' => 2,
                                       'domain' => $domain.'.'.$this->domain
                                       ));
	}

    public function dumpZone(){
		return $this->getData('dumpzone', array('domain' => $this->domain));
    }
/**
 * Get Data
 * 
 * @access public
 * @return object
 */
	public function addZoneRecord($name, $type, $class, $address=null){

		if(!$address){
			$address = gethostbyname($this->domain);
		}
		return $this->addItem('addzonerecord', array(
													'zone' => $this->domain,
													'name' => $name,
													'address' => $address,
													'type' => $type,
													'class' => $class
							  ));
 	}

/**
 * Get Data
 * 
 * @access private
 * @return object
 */
	private function getData($function, $params = null){
		$this->getConfig();

		$url = $this->ssl ? 'https://' : 'http://';
		$url .= $this->domain.':'.$this->port.'/json-api/'.$function;

		if($params && is_array($params)){
			$url .= '?'.http_build_query($params);
		}

		$ch = curl_init();

		$this->logger->debug($url);

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);	
		# Allow self-signed certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		if($this->hash){
			$header[0] = "Authorization: WHM ".$this->user.":" . preg_replace("'(\r|\n)'","",$this->hash);
		} else {
			$header[0] = "Authorization: Basic " . base64_encode($this->user.":".$this->pass) . "\n\r";
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// grab URL and pass it to the browser
		$ret = curl_exec($ch);

		$this->logger->debug('CPANEL getData: '.print_r($ret,1));

		$ret = json_decode($ret);

		// close cURL resource, and free up system resources
		curl_close($ch);

		return $ret;
	}

/**
 * Add Item
 * 
 * @access private
 * @param string $function
 * @param array  $params 
 * @return object
 */
	private function addItem($function, $params){
		$this->getConfig();

		$query = http_build_query($params);

		$url = $this->ssl ? 'https://' : 'http://';
		$url = $this->domain.':'.$this->port.'/json-api/'.$function.'?'.$query;

		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);	
		# Allow self-signed certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		if($this->hash){
			$header[0] = "Authorization: WHM ".$this->user.":" . preg_replace("'(\r|\n)'","",$this->hash);
		} else {
			$header[0] = "Authorization: Basic " . base64_encode($this->user.":".$this->pass) . "\n\r";
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// grab URL and pass it to the browser
		$ret = json_decode(curl_exec($ch));

		// close cURL resource, and free up system resources
		curl_close($ch);

		if($ret->result->status == 1){
			return true;
		}else {
			return false;
		}
	}


/**
 * Get Config
 * 
 * @access private
 * @return void
 */
	private function getConfig(){
		$this->port    = $this->config->application[$this->package]['connect']['port'];
		$this->ssl     = $this->config->application[$this->package]['connect']['ssl'] == 'Yes' ? true : false;
		$this->domain  = $this->config->application[$this->package]['connect']['domain'];
		$this->user    = $this->config->application[$this->package]['connect']['user'];
		$this->pass    = $this->config->application[$this->package]['connect']['pass'];
		$this->userdir = $this->config->application[$this->package]['connect']['userdir'];
	}

/**
 * Set Package
 * 
 * Set Package to grab from Config
 *
 * @access public
 * @param  string $package
 * @return void
 */
	public function setPackage($package){
		$this->package = $package;
		$this->getConfig();
	}
}
