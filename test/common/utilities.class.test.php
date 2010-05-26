<?php  //cut_tests.php
class UtilitiesUnitTestCase extends UnitTestCase{
	public function __construct() {
		$this->UnitTestCase('Testing the utilities functions');
	}
	
	//normal unit test cases  
	public function test_underscores_to_camelcase_normal() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
	}
	
	public function test_format_seconds_to_hours_normal(){
		$s = Utilities::format_seconds_to_hours('3600');
		$this->assertTrue($s === '1:00:00');
	}
	
	public function test_format_seconds_to_minutes_normal(){
		$s = Utilities::format_seconds_to_minutes('60');
		$this->assertTrue($s === '01:00');
	}
	
	//null unit test cases
	public function test_format_seconds_to_hours_null(){
		//$variable = null;
		$s = Utilities::format_seconds_to_hours($variable);
		$this->assertNull($s);
	}
	
	public function test_format_seconds_to_minutes_null(){
		//$variable = null;
		$s = Utilities::format_seconds_to_minutes($variable);
		$this->assertNull($s);
		
	}
	
	//isnotnull unit test cases
	public function test_format_seconds_to_hours_not_null(){
		$s = Utilities::format_seconds_to_hours('3600');
		$this->assertNotNull($s === '1:00:00');
	}
	
	public function test_format_seconds_to_minutes_not_null(){
		$s = Utilities::format_seconds_to_minutes('60');
		$this->assertNotNull($s === '01:00');
	}
	
	public function test_truncate_string()
	{
	    $s = Utilities :: truncate_string('Testing the utilities functions', 10);
	    $this->assertTrue($s === 'Testing&hellip;');
	}
	
	public function test_is_html_document()
	{
	    $s = Utilities :: is_html_document('index.html');
	    $this->assertTrue($s === true);
	}
}
?>