<?php
class AttributeClass extends Attribute
{
	public function AttributeClass($class)
	{
		parent::Attribute($class);
	}
	
	public function get_attribute()
	{
		$text = 'class="'. $this->attribute .'"';		
		return $text;
	}
}
?>