<?php
require_once '../lib/activerecord/DbTable.php';

class Comment extends DbTable {
	
	protected $_belongs_to = "post";
	
	
}