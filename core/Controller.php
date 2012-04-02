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
 * Controller
 * 
 * View and Data FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */

require_once('lib/Minify_HTML.php');

/**
 * Controller Class
 * 
 * @package FrameD
 * @subpackage core
 */
abstract class Controller{

   /**
    * Data used when Rendering
    * 
    * @access private
    * @var mixed
    */
    private $data;

   /**
    * Meta Data used when Rendering
    * 
    * @access private
    * @var mixed
    */
    private $metadata;

   /**
    * Rendering Data Format
    * 
    * @access protected
    * @var mixed
    */
    protected $format;

   /**
    * Logger Object
    * 
    * @access public
    * @var Logger Object
    */
    public $logger;

   /**
    * Config Loader Object
    * 
    * @access public
    * @var ConfigLoader Object
    */
    public $configLoader;

   /**
    * Session Data
    * 
    * @access public
    * @var mixed
    */
    public $sessionData;

	/**
    * Plugin Loader
    * 
    * @access public
    * @var mixed
    */
	public $pluginLoader;

/**
 * Controller Construct
 * 
 * @param string $format Rendering Data format
 * @return void
 */
	function __construct($format, $class, $cameFrom){
		$this->format = $format;
		$this->cameFrom = $cameFrom;
		$this->sessionData = new SessionData();
		$this->logger = new Logger($class);
		$this->config = new Config();
		$this->dbLoader = new DbLoader();
		$this->configLoader = new ConfigLoader();
		$this->pluginLoader = new PluginLoader();
    }

/**
 * Cache Control
 * 
 * @access private
 * @param  int $expires Time in seconds DEFAULT 15 minutes
 * @return void
 */
        public function cacheControl($expires=900){
                if (strtotime(trim($_SERVER['HTTP_IF_NONE_MATCH']).' GMT') >= time()) {
                    header("HTTP/1.1 304 Not Modified");
                    exit;
                }
                $expires = 60*15;
                header("Pragma: public");
                header("Cache-Control: max-age=".$expires);
                header('Expires: ' . gmdate('D, d M Y H:i:s', (time()+$expires)) . ' GMT');
                header('Etag: '.gmdate('D, d M Y H:i:s', (time()+$expires)));
                header("Last-Modified: ". gmdate('D, d M Y H:i:s', time()) . " GMT");

                return;
        }

/**
 * No Cache Control
 * 
 * @access public
 * @return void
 */
        public function noCacheControl(){
                header("Pragma: no-cache");
                header("Cache-Control: no-cache, must-revalidate");
                header('Expires: ' . gmdate('D, d M Y H:i:s', (time()-60*15)) . ' GMT');
                header('Etag: '.gmdate('D, d M Y H:i:s', (time()-60*15)));
                header("Last-Modified: ". gmdate('D, d M Y H:i:s', time()) . " GMT");

                return;
        }

/**
 * Set Response Header
 * 
 * @access public
 * @return void
 */
	public function setResponseHeader($httpCode = 200){
		$this->httpCode = $httpCode;
	}

/**
 * Call Response Header
 * 
 * @access private
 * @return void
 */
	private function callResponseHeader(){
		$httpCodeConfig = $this->configLoader->load('core_httpcodes');
		header('HTTP/1.0 ' . $httpCodeConfig['HTTPCODE'][$this->httpCode]);
	}

/**
 * Render
 * 
 * @access public
 * @param string $view View to be rendered
 * @return void
 */
	public function render($view=NULL){

		if($this->cameFrom != 'cli'){
				header('Set-Cookie: '.$this->config->environment['SESSIONDATA']['cookie']['name'].'='.
				 urlencode($this->sessionData->getEncrypted()).'; HttpOnly');
		}


		ob_start();
		$data = $this->data;

		if($this->metadata){
				foreach($this->metadata as $key => $value){
					${$key} = $value;
				}
		}

		//$this->callResponseHeader();

		if($this->format == '' || strtolower($this->format) == 'html' || strtolower($this->format) == 'php' && $view != NULL){

		   $logger = new Logger($view);
		   $config = $this->config;
		   if(is_file('app/views/html/'.$view.'.php')){

			   $this->logger->trace('LOADING VIEW: '.$view);
			   require('app/views/html/'.$view.'.php');

		   } elseif(is_file('core/app/views/html/'.$view.'.php')){

			   $this->logger->trace('LOADING CORE VIEW: '.$view);
			   require('core/app/views/html/'.$view.'.php');

		   }
			
		   if($this->config->application['SERVER']['minify_html'] == 'On'){
				   $return = ob_get_contents();

				   ob_end_clean();

				   $minify = new Minify_HTML($return, array()); 

				   echo $minify->process();
		   }else {
				ob_end_flush();
		   }
		} else {
		    $logger = new Logger($view);
			if(is_file('app/views/'.$this->format.'/'.$view.'.php')){

			    require('app/views/'.$this->format.'/'.$view.'.php');

			} elseif(is_file('core/app/views/'.$this->format.'/'.$view.'.php')){

				require('core/app/views/'.$this->format.'/'.$view.'.php');

			} else {

			  $this->renderFormat($data);

			}
		}


		return;
	}

/**
 * Set View Data
 * 
 * @access public
 * @param string $data Data to be used when Rendering
 * @return void
 */
	public function setViewData($data){
		$this->data = $data;

		return;
    }

/**
 * Add View Meta
 * 
 * @access public
 * @param string $key Name of Data to be used when Rendering
 * @param string $data Data to be used when Rendering
 * @return void
 */
	public function addViewMeta($key, $data){
		$this->metadata[$key] = $data;

		return;
    }
/**
 * Render Format
 * 
 * @access private
 * @param string $data Data to transform for rendering
 * @return void
 */
	private function renderFormat($data){
		switch(strtolower($this->format)){
			case 'json':
			case 'js':
				header('Content-Type: application/json');
				echo $this->writeJSON($data);
			break;
			case 'data':
				echo "<pre>Data:\n\n";
				print_r($data);
				echo '</pre>';
			break;
			case 'text':
				if(is_array($data)){
					print_r($data);
				} else {
					echo $data;
				}
			break;

			case 'xml':
				header('Content-Type: text/xml');
				echo $this->writeXML($data);

			break;
		}
	}

/**
 * Render Return
 * 
 * @access private
 * @param string $view View to be rendered
 * @return view data
 */
	public function setReturnFormat($format){

		$this->altFormat = $format;

		return;
	}

/**
 * Render Return
 * 
 * @access private
 * @param string $view View to be rendered
 * @return view data
 */
	public function renderReturn($view=NULL){
		ob_start();
		$data = $this->data;
		$format = $this->altFormat ? $this->altFormat : $this->format;

		if(is_file('app/views/'.$format.'/'.$view.'.php')){
			require('app/views/'.$format.'/'.$view.'.php');
		}elseif(is_file('core/app/views/'.$format.'/'.$view.'.php')){
			require('core/app/views/'.$format.'/'.$view.'.php');
		}

		$return = ob_get_contents();

		ob_end_clean();

		return $return;
	}

/**
 * Write JSON
 * 
 * Writes data as JSON
 *
 * @access private
 * @param  string  $data Package data
 * @return string  JSON
 */
    private function writeJSON($data){
		if(!class_exists('Services_JSON')){
                require_once('JSON.php');
        }
		$json = new Services_JSON();

		return $json->encode($data);
    }


/**
 * Write XML
 * 
 * Writes data as XML
 *
 * @access private
 * @param  string  $array Package data
 * @return string  XML
 */
	private function writeXML($array){
		if(!class_exists('XML_Serializer')){
				require_once('XML/Serializer.php');
		}

			$options = array(
				XML_SERIALIZER_OPTION_INDENT      => "\t",     // indent with tabs
				XML_SERIALIZER_OPTION_RETURN_RESULT => true,
				XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",     // use UNIX line breaks
				XML_SERIALIZER_OPTION_ROOT_NAME   => 'data',// root tag
				XML_SERIALIZER_OPTION_DEFAULT_TAG => 'item'    // tag for values 
															   // with numeric keys
		   );
		 
			$serializer = new XML_Serializer($options);
    		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"."\n".$serializer->serialize($array);
	}
}
