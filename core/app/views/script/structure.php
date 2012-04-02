<?php 

echo 
'<?php 
/**
 * '.$data['table'].' Structure
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package '.$data['dbName'].'
 */

/**
 * '.$data['dbType'].' Database Object
 */
require_once("core/databases/'.$data['dbType'].'Db.php");

/**
 * '.$data['table'].'Stucture Class
 * 
 * @package '.$data['dbName'].'
 * @subpackage models
 */
class '.$data['table'].'Structure extends Model{

  /**
    * Database Type
    * 
    * @access public
    * @var string
    */
  public $dbClass = "'.$data['dbType'].'Db'.'";

  /**
    * Database Name
    * 
    * @access public
    * @var string
    */
  public $dbName = "'.$data['dbName'].'";

  /**
    * Database Name
    * 
    * @access public
    * @var string
    */
  public $dbCfg = "'.$data['dbCfg'].'";

  /**
    * Table Name
    * 
    * @access public
    * @var string
    */
  public $tableName = "'.$data['tableName'].'";

  /**
    * Table Data
    * 
    * @access public
    * @var array
    */
  public $tableData = array(
';


foreach($data['columns'] as $index => $column){
  $temp1[$index] = "\t".$index.' => array('."\n";
  $temp = array();
	foreach($column as $key => $value){
		$temp[] = "\t\t".'"'.$key.'" => "'.$value.'"';
	}
  $temp1[$index] .= implode(",\n",$temp).')';
}

echo implode(",\n",$temp1);

echo '
          );';

echo '

  function __construct(){
	parent::__construct($this->tableName);
	$tableName = $this->tableName;

	$dbLoader = new DbLoader();

	$this->db = $dbLoader->load($this->dbCfg);

	$this->db->$tableName = $this->tableData;
  }
}
?>'; 
?>
