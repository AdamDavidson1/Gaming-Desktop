<?php 

/**
 * Default Root Controller
 * 
 * Default root directory actions
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package Gaming
 */

/**
 * RootController Class
 * 
 * @package FrameD
 * @subpackage app
 */
class RootController extends Controller {

   public function web_index(AuthBin $auth){
		$this->render('index');
   }
   public function web_table(AuthBin $auth,
							 PayloadPkg $id){
		$this->render('table');
   }
}
