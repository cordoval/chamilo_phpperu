<?php

class EmptyObjectExport{
	
	private static $instance = null;
	
	public static function get_instance(){
		self::$instance = empty(self::$instance) ? new self() : self::$instance;
		return self::$instance;
	}
	
    public function export_content_object(){
    	return false;
    }
}





?>