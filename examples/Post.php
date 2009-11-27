<?php
require_once '../lib/activerecord/DbTable.php';

class Post extends DbTable {
	
	protected $_has_many = array('comments');
	
	
} 