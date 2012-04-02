<?php 
/**
 * Default Socket Root Controller
 * 
 * Default cli root directory actions
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package Example
 */

class SocketController extends Controller {

	public function cli_chat_socket(PayloadPkg $startPort){
		set_time_limit(0);
		ob_implicit_flush();

		$master  = $this->WebSocket("localhost",$startPort->getInt());
		$this->sockets = array($master);
		$this->users   = array();
		$this->debug   = false;
		$this->policy_file = file_get_contents('www/pub/policy_file.xml');

		while(true){
		  $changed = $this->sockets;
		  socket_select($changed,$write=NULL,$except=NULL,NULL);
		  foreach($changed as $socket){
			if($socket==$master){
			  $client=socket_accept($master);
			  if($client<0){ $this->console("socket_accept() failed"); continue; }
			  else{ $this->connect($client); }
			}
			else{
				$this->logger->debug('Receiving');
			  $bytes = @socket_recv($socket,$buffer,2048,0);
			  if($bytes==0){ $this->disconnect($socket); }
			  else{
				$user = $this->getuserbysocket($socket);
				if(!$user->handshake){ $this->dohandshake($user,$buffer); }
				else{ $this->process($user,$buffer); }
			  }
			}
		  }
		}
	}

	private function process($user,$msg){
		  $action = $this->unwrap($msg);
		  $this->say("< ".$action);
		  switch($action){
			case "hello" : $this->send($user->socket,"hello human");                       break;
			case "hi"    : $this->send($user->socket,"zup human");                         break;
			case "name"  : $this->send($user->socket,"my name is Multivac, silly I know"); break;
			case "age"   : $this->send($user->socket,"I am older than time itself");       break;
			case "date"  : $this->send($user->socket,"today is ".date("Y.m.d"));           break;
			case "time"  : $this->send($user->socket,"server time is ".date("H:i:s"));     break;
			case "thanks": $this->send($user->socket,"you're welcome");                    break;
			case "bye"   : $this->send($user->socket,"bye");                               break;
			default      : $this->send($user->socket,$action." not understood");           break;
		  }
	}

	private function send($client,$msg){
  		$this->say("> ".$msg);
  		$msg = $this->wrap($msg);
  		socket_write($client,$msg,strlen($msg));
	}

		private function WebSocket($address,$port){
		  $master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
		  socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
		  socket_bind($master, $address, $port)                    or die("socket_bind() failed");
		  socket_listen($master,20)                                or die("socket_listen() failed");
		  echo "Server Started : ".date('Y-m-d H:i:s')."\n";
		  echo "Master socket  : ".$master."\n";
		  echo "Listening on   : ".$address." port ".$port."\n\n";
		  return $master;
		}

		private function connect($socket){
		  $this->logger->debug('CONNECTING');
		  $user = new User();
		  $user->id = uniqid();
		  $user->socket = $socket;
		  array_push($this->users,$user);
		  array_push($this->sockets,$socket);
		  $this->console($socket." CONNECTED!");
		}

		private function disconnect($socket){
		  $found=null;
		  $n=count($this->users);
		  for($i=0;$i<$n;$i++){
			if($users[$i]->socket==$socket){ $found=$i; break; }
		  }
		  if(!is_null($found)){ array_splice($users,$found,1); }
		  $index = array_search($socket,$this->sockets);
		  socket_close($socket);
		  $this->console($socket." DISCONNECTED!");
		  if($index>=0){ array_splice($this->sockets,$index,1); }
		}

		private function dohandshake($user,$buffer){
		  $this->console("\nRequesting handshake...");
		  $this->console($buffer);
		  if(preg_match('<policy-file-request/>',$buffer)){
			socket_write($user->socket, $this->policy_file.chr(0), strlen($this->policy_file.chr(0)));
			return true;
		  }
		  list($resource,$host,$origin,$strkey1,$strkey2,$data) = $this->getheaders($buffer);
		  $this->console("Handshaking...");

		  $pattern = '/[^\d]*/';
		  $replacement = '';
		  $numkey1 = preg_replace($pattern, $replacement, $strkey1);
		  $numkey2 = preg_replace($pattern, $replacement, $strkey2);

		  $pattern = '/[^ ]*/';
		  $replacement = '';
		  $spaces1 = strlen(preg_replace($pattern, $replacement, $strkey1));
		  $spaces2 = strlen(preg_replace($pattern, $replacement, $strkey2));

		  if ($spaces1 == 0 || $spaces2 == 0 || $numkey1 % $spaces1 != 0 || $numkey2 % $spaces2 != 0) {
				socket_close($user->socket);
				$this->console('failed');
				return false;
		  }

		  $ctx = hash_init('md5');
		  hash_update($ctx, pack("N", $numkey1/$spaces1));
		  hash_update($ctx, pack("N", $numkey2/$spaces2));
		  hash_update($ctx, $data);
		  $hash_data = hash_final($ctx,true);

		  $upgrade  = "HTTP/1.1 101 WebSocket Protocol Handshake\r\n" .
					  "Upgrade: WebSocket\r\n" .
					  "Connection: Upgrade\r\n" .
					  "Sec-WebSocket-Origin: " . $origin . "\r\n" .
					  "Sec-WebSocket-Location: ws://" . $host . $resource . "\r\n" .
					  "\r\n" .
					  $hash_data;

		  socket_write($user->socket,$upgrade.chr(0),strlen($upgrade.chr(0)));
		  $user->handshake=true;
		  $this->console($upgrade);
		  $this->console("Done handshaking...");
		  return true;
		}

		private function getheaders($req){
		  $r=$h=$o=null;
		  if(preg_match("/GET (.*) HTTP/"   ,$req,$match)){ $r=$match[1]; }
		  if(preg_match("/Host: (.*)\r\n/"  ,$req,$match)){ $h=$match[1]; }
		  if(preg_match("/Origin: (.*)\r\n/",$req,$match)){ $o=$match[1]; }
		  if(preg_match("/Sec-WebSocket-Key2: (.*)\r\n/",$req,$match)){ $key2=$match[1]; }
		  if(preg_match("/Sec-WebSocket-Key1: (.*)\r\n/",$req,$match)){ $key1=$match[1]; }
		  if(preg_match("/\r\n(.*?)\$/",$req,$match)){ $data=$match[1]; }
		  return array($r,$h,$o,$key1,$key2,$data);
		}

		private function getuserbysocket($socket){
		  $found=null;
		  foreach($this->users as $user){
			if($user->socket==$socket){ $found=$user; break; }
		  }
		  return $found;
		}

		private function     say($msg=""){ echo $msg."\n"; }
		private function    wrap($msg=""){ return chr(0).$msg.chr(255); }
		private function  unwrap($msg=""){ return substr($msg,1,strlen($msg)-2); }
		private function console($msg=""){ $this->logger->debug($msg); if($this->debug){ echo $msg."\n"; } }
}
		class User{
		  var $id;
		  var $socket;
		  var $handshake;
		}
