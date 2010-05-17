<?php

function cut_string($string, $size)
{
	return substr($string, 0, $size);
}

class TestExample extends UnitTestCase
{
	public function __construct()
	{
		parent :: __construct('Testing the cutting functions');
	}
	
	public function test_cut_string_normal()
	{
		$string = 'abcde';
		$s = cut_string($string, 4);
		$this->assertTrue(strlen($s) < 5);
	}
	
	public function test_cut_string_integer()
	{
		$string = 123;
		$s = cut_string($string, 1);
		$this->assertTrue(is_string($s));
	}
}
?>