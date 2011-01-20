<?php
class Disabled extends Attribute
{
	public function Disabled()
	{
		parent::Attribute('disabled="disabled"');
	}
	
	public function get_attribute()
	{
		return $this->attribute;
	}
}
?>