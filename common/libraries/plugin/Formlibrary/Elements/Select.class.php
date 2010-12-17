<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Selectlanguage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Selectlanguage.class.php';
/*
 * Class to create a Select element
 */
class Select extends Element
{
	protected $options;              // array: the options of the selectfield
	protected $size;                 // integer: set the size of the field
	
	/*
	 * Constructor for the select class
	 */	
	public function Select($name, $label, $options)
	{
		// call the constructor of the Element class
		parent::Element($name, null, $label);
		$this->options = $options;
		$this->size = 1;
	}

	/*
	 * This function returns the HTML of the created select field
	 */
	public function render()
	{		
		$html = array();
		$html[] = '<select name="' . $this->name.'"'. $this->attributestorage->get_attributes().'>';
		foreach($this->options as $key => $value)
		{
			/*if($key == $this->get_value())  
				$html[] = 'selected="selected"'; 
			else $html[]= '';*/
			if($key == $this->value)
			{
				$string1 = '<option selected="selected"'. $text .'value="' . $key. '">' . $value;			
				$string1 .= "</option>";
				$html[] = $string1;
			}
			else 
			{
				$string1 = '<option '. $text .'value="' . $key. '">' . $value;			
				$string1 .= "</option>";
				$html[] = $string1;
			}	
		}
		$html[] = '</select>';		
		return implode('\n', $html);		
	}
}
?>