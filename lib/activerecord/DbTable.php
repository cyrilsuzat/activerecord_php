<?php
require_once 'Db.php';
require_once 'Inflect.php';

abstract class DbTable {
	
	// The table name.
    protected $_name = null;

	// The table columns as key=>value
	protected $_attributes = array();
	
	// The parent table
	protected $_belongs_to = null;
	
	// The dependent tables
	protected $_has_many = array();
	
	function __construct($params=null) {
		if(!$this->_name) $this->_name = strtolower(Inflect::pluralize(get_class($this)));
		
		if($params):
		foreach ($params as $key => $value) {
			if($key[0] != '_') // keys starting by underscore (_) are ignored
				$this->$key = $value;
		}
		endif;
	}
	
	function __get($name) {
		if(in_array($name, $this->_has_many)) {
			$called_class = ucfirst(strtolower(Inflect::singularize($name)));
			$foreign_key = Inflect::singularize($this->_name) . '_id';
			$result = self::find('all', array('called_class' => $called_class, 'where' => "$foreign_key = $this->id"));
			
			return $result;
		}
		if($name == $this->_belongs_to) {
			$called_class = ucfirst($name);
			$foreign_key = $name . '_id';
			$result = self::find($this->$foreign_key, array('called_class' => $called_class));
			
			return $result;
		}
		
		return $this->_attributes[$name];
	}
	
	function __set($name, $value) {
		$this->_attributes[$name] = $value;
	}
	
	public static function find($id='all', $options=null) {
		$called_class = ($options['called_class']) ?
			$options['called_class'] : self::get_called_class();	
		$class = new $called_class();
		$columns = ($options['columns']) ? implode($options['columns'], ', ') : "*";
		if($id=='all') {
			$sql = "SELECT $columns FROM $class->_name";
			if ($options['where']) $sql .= " WHERE " . $options['where'];
			if ($options['order']) $sql .= " ORDER BY " . $options['order'];
			if ($options['limit']) $sql .= " LIMIT " . $options['limit'];
			if ($options['offset']) $sql .= " OFFSET " . $options['offset'];
			$result = Db::query($sql);
			while ($row = mysql_fetch_assoc($result)) {
				$new_class = new $class($row);
				$tab[] = $new_class;
			}
			return $tab;
		}
		else {
			$id = intval(mysql_real_escape_string($id));
			$sql = "SELECT $columns FROM $class->_name WHERE id = $id";
			$result = Db::query($sql);
			if(mysql_num_rows($result)>0) {
				$attributes = mysql_fetch_assoc($result);
				foreach ($attributes as $key => $value) {
					$class->$key = $value;
				}
				return $class;
			}
		}
		return false;
	}
	
	function save() {
		if($this->id) // update
			return $this->update_attributes($this->_attributes);

		// insert
		$attributes = self::escape_attributes($this->_attributes);
		$columns = implode(array_keys($attributes), ', ');
		$values = implode($attributes, ', ');
		$created_at = "'" . date('Y-m-d H:i:s') . "'";
		$sql = "INSERT INTO $this->_name ($columns, created_at) VALUES ($values, $created_at)";
		Db::query($sql);
		
		return mysql_insert_id();
	}
	
	function update_attributes($attributes) {
		$attributes = self::escape_attributes($attributes);
		$updated_at = "'" . date('Y-m-d H:i:s') . "'";
		foreach ($attributes as $key => $value) {
			if($key != 'id') {
				$set .= "$key = $value, ";
				$this->$key = substr($value, 1, -1);
			}
		}
		$set .= "updated_at = $updated_at";
		$sql = "UPDATE $this->_name SET $set WHERE id = $this->id";
		Db::query($sql);
		
		return $this->id;
	}
	
	function destroy() {
		$sql = "DELETE FROM $this->_name WHERE id = $this->id LIMIT 1";
		
		return Db::query($sql);
	}
	
	// escape attributes to get a valid SQL string 
	// and protect against SQL injections
	static function escape_attributes($attributes) {
		foreach ($attributes as $key => $value) {
			$attributes[$key] = "'" . mysql_real_escape_string($value) . "'";
		}
		return $attributes;
	} 
	
	// TO DELETE if using PHP>=5.3.0
	// http://php.net/function.get_called_class
	static function get_called_class()
	{
	    $bt = debug_backtrace(); //var_dump($bt); echo "<p></p>";
	    if (isset($bt[1]['file']) && isset($bt[1]['line']) && isset($bt[1]['function']))
	    {
	        $lines = file($bt[1]['file']);
	        $pattern = '/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/';
	        preg_match($pattern, $lines[$bt[1]['line']-1], $matches);
			if (count($matches)>0)
	          return $matches[1];
	    }
	    return false;
	}
	
	function to_xml($base_tag=null) {
		$base = ($base_tag) ? $base_tag : Inflect::singularize($this->_name);
		$xml = "<$base>\n";
		foreach ($this->_attributes as $key=>$value) {
			$xml .= "<$key>$value</$key>\n";
		}
		$xml .= "</$base>\n";
		
		return $xml;
	}
}