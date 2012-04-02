<?php 
/**
 * User Structure
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package framed
 */

/**
 * SQLite3 Database Object
 */
require_once("core/databases/SQLite3Db.php");

/**
 * UserStucture Class
 * 
 * @package framed
 * @subpackage models
 */
class UserStructure extends Model{

  /**
    * Database Type
    * 
    * @access public
    * @var string
    */
  public $dbClass = "SQLite3Db";

  /**
    * Database Name
    * 
    * @access public
    * @var string
    */
  public $dbName = "framed";

  /**
    * Database Name
    * 
    * @access public
    * @var string
    */
  public $dbCfg = "framed";

  /**
    * Table Name
    * 
    * @access public
    * @var string
    */
  public $tableName = "User";

  /**
    * Table Data
    * 
    * @access public
    * @var array
    */
  public $tableData = array(
	0 => array(
		"cid" => "0",
		"name" => "id",
		"type" => "INTEGER",
		"notnull" => "0",
		"dflt_value" => "",
		"pk" => "1"),
	1 => array(
		"cid" => "1",
		"name" => "name",
		"type" => "TEXT",
		"notnull" => "0",
		"dflt_value" => "",
		"pk" => "0"),
	2 => array(
		"cid" => "2",
		"name" => "added_datetime",
		"type" => "DATE",
		"notnull" => "0",
		"dflt_value" => "",
		"pk" => "0")
          );

  function __construct(){
	parent::__construct($this->tableName);
	$tableName = $this->tableName;

	$dbLoader = new DbLoader();

	$this->db = $dbLoader->load($this->dbCfg);

	$this->db->$tableName = $this->tableData;
  }
}
?>