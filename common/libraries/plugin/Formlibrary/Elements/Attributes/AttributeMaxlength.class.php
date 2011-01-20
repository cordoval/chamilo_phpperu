<?php
class AttributeMaxlength extends Attribute
{
	public function AttributeMaxlength($max)
	{
		parent::Attribute($max);
	}
	
	public function get_attribute()
	{
		$text = 'maxlength="'. $this->attribute .'"';		
		return $text;
	}
}
?>