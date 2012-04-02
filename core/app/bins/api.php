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
 * ApiBin
 * 
 * Api Bin that allows generic Api access for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * ApiBin Class
 * 
 * @package FrameD
 * @subpackage core
 */
class ApiBin extends Bin{

/**
 * Load
 *  
 * @access public
 * @param  string $columns        Columns for Selection
 * @param  string $order          Order of Selection
 * @param  string $group          Grouping for Selection
 * @param  string $where          Where for Selection
 * @param  string $whereGreater   Where Greater for Selection
 * @param  string $whereLess      Where Less than for Selection
 * @param  string $whereGreaterEq Where Greater Equal to for Selection
 * @param  string $whereLessEq    Where Less than or Equal to for Selection
 * @param  string $or             Where Or for Selection
 * @return array  db rows
 */
	public function load(
						 PayloadPkg $columns,
						 PayloadPkg $order,
						 PayloadPkg $group,
						 PayloadPkg $where,
						 PayloadPkg $whereGreater,
						 PayloadPkg $whereLess,
						 PayloadPkg $whereGreaterEq,
						 PayloadPkg $whereLessEq,
						 PayloadPkg $or,
						 PayloadPkg $not
						){


		$this->columns = $columns->getHash(',');
		$this->order = $order->getHash(',');
		$this->or = $or->getHash(',');
		$this->not = $not->getHash(',');
		$this->group = $group->getHash(',');
		$this->where = $where->getHash(',');
		$this->whereGreater = $whereGreater->getHash(',');
		$this->whereLess = $whereLess->getHash(',');
		$this->whereGreaterEq = $whereGreaterEq->getHash(',');
		$this->whereLessEq = $whereLessEq->getHash(',');
		
	}

	public function getData($modelName, $package){
		$model = $this->modelLoader->load($modelName,$package);

		foreach($this->whereGreater as $index => $whereGreater){
			$this->where[$index.'>'] = $whereGreater;
		}
		foreach($this->whereLess as $index => $whereLess){
			$this->where[$index.'<'] = $whereLess;
		}
		foreach($this->whereGreaterEq as $index => $whereGreaterEq){
			$this->where[$index.'>='] = $whereGreaterEq;
		}
		foreach($this->whereLessEq as $index => $whereLessEq){
			$this->where[$index.'<='] = $whereLessEq;
		}
		foreach($this->not as $index => $not){
			$this->where[$index.'!'] = $not;
		}

		if($this->group){
			$addendum['group'] = $this->group;
		}
		if($this->order){
			$addendum['order'] = $this->order;
		}

		if(!$this->columns){
			$this->columns = null;
		}

		return $model->smartSelect($this->columns, $this->where, $addendum);
    }
}
?>
