<?php
require_once 'config.php';

class Db {
	
	private $db_adapter;
	
	function __construct() {
		$this->db_adapter = mysql_pconnect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD) or trigger_error(mysql_error(),E_USER_ERROR);
		mysql_select_db(DB_DATABASE, $this->db_adapter);
	}
	
	function __destruct() {
		// @todo Drop the connection to the database
	}
	
	public static function get_db_adapter() {
		$db = new Db();
		return $db->db_adapter;
	}
	
	public static function query($sql) {
		$db = new Db();
		return mysql_query($sql, $db->db_adapter);
	}
}