<?php
/*
 * Class to create a passwordfield, it inherits from Element class
 */

class Password extends Element
{
	/*
	 * Constructor for the Password class
	 */
	public function Password($name, $label)
	{
		//call constructor of Element class
		parent::Element($name, "", $label);		
	}
	
	/*
	 * Returns the HTML of the created passwordfield
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf(		
			'<input type="password" name="%s" id="%1$s" "%s" '.'>',
			$this->name,
			$this->attributestorage->get_attributes());
		return implode('\n', $html);				
	}
}
?>