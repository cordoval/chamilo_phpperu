<?php
/*
 * Class to create a hidden field
 */
class Hidden extends Element
{
	//Constructor of the Hidden class
	public function Hidden($name, $value= null)
	{
		parent::Element($name, $value);
	}
	
	/*
	 * This function returns the HTML of the created hidden field
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf(
          '<input type="hidden" name="%s" id="%1$s" value="%s" %s'.'>',
          $this->name,
          $this->value,
          $this->attributestorage->get_attributes()
          );
    	return implode('\n', $html);    
	}
}
?>