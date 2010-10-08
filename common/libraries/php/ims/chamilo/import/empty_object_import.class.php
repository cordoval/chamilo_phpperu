<?php

class EmptyObjectImport{
	
	private static $instance = null;
	
	public static function get_instance(){
		self::$instance = empty(self::$instance) ? new self() : self::$instance;
		return self::$instance;
	}
	
    public function import_content_object(){
    	return null;
    }
}





?>