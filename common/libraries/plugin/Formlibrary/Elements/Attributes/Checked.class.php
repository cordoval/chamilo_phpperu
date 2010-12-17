<?php
class Checked extends Attribute
{
	public function Checked()
	{
		parent::Attribute('checked="checked"');
	}
	
	public function get_attribute()
	{
		return $this->attribute;
	}
}
?>