<?php  //cut_tests.php
class UtilitiesUnitTestCase extends UnitTestCase{
	public function __construct() {
		$this->UnitTestCase('Testing the utilities functions');
	}
	public function test_underscores_to_camelcase_normal() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
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