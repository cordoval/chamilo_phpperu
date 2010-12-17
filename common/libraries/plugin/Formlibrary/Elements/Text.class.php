<?php
/*
 * Class to create a textfield, it inherits from Element class
 */

class Text extends Element
{
	/*
	 * Constructor for the Text class
	 */
	public function Text($name, $value , $label)
	{
		//call constructor of Element class
		parent::Element($name, $value, $label);	
	}
	
	/*
	 * Returns the HTML of the created textfield
	 */
	public function render()
	{
		$val = "";
		if(isset($_POST[$this->get_name()])) 
			$val = htmlspecialchars($this->get_value());
		else $val = $this->value;
		$html = array();
		$html[] = sprintf(		
			'<input type="text" name="%s" id="%1$s" value="%s" "%s" '.'> ',
			$this->name,
			$val,
			$this->attributestorage->get_attributes());
		return implode('\n', $html);	
	}
}
?>