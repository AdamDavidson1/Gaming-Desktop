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
 * Default Error Controller
 * 
 * Default error directory actions
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package Example
 */

/**
 * ErrorController Class
 * 
 * @package FrameD
 * @subpackage core
 */
class ErrorController extends Controller {

   public function web_404(){
		$this->render('errors/404');
   }	

   public function web_500(){
		$this->render('errors/500');
   }	

   public function web_403(){
		$this->render('errors/403');
   }	
}
?>
