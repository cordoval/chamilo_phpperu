<?php
class Readonly extends Attribute
{
	public function Readonly()
	{
		parent::Attribute('readonly="readonly"');
	}
	
	public function get_attribute()
	{
		return $this->attribute;
	}
}
?>