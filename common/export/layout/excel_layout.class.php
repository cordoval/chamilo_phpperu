<?php
/**
 * $Id: layout.class.php 2010-06-23 11:05:20Z Shoira Mukhsinova $
 * @package common.export.excel.layout
 */


class ExcelLayout {
	
	private $background_question; 
	private $background_answer;
	
	function ExcelLayout()
    {
    }
	
	public function set_background_question($color){
		$this->_background_question = $color;
	}
	
	public function get_background_question(){
		return $this->_background_question;
	}
	
	public function set_background_answer($color){
		$this->_background_answer = $color;
	}
	
	public function get_background_answer(){
		return $this->_background_question;
	}
}
?>