#!/usr/bin/php -q
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
 * Cli Indexer
 * 
 * Bootstrapping for Cli
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */

set_include_path(get_include_path() . ':../');

ini_set('display_errors',false);
ini_set('log_errors',1);
ini_set('error_log', 'logs/phperrors.log');

$cameFrom = 'cli';
foreach($_SERVER['argv'] as $index => $arg){
	if(substr_count('--',$arg)){
		$item = str_replace('--','',$arg);

		${$item} = $_SERVER['argv'][$index+1];
	}
}

/**
 * FrameD Core
 */
require_once('core/core.php');

$format   = 'text';

loadFramework($cameFrom, $controller, $action, $format);

?>
