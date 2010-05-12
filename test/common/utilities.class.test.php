<?php  //cut_tests.php
class UtilitiesUnitTestCase extends UnitTestCase{
	public function __construct() {
		$this->UnitTestCase('Testing the utilities functions');
	}
	public function test_underscores_to_camelcase_normal() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
	}
	public function test_underscores_to_camelcase_strcmp() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue(strcmp($s,'AbcDef')===0);
	}
}
?>

