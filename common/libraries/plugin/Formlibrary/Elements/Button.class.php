<?php
/*
 * Class for a button, you can add an onclick action via javascript
 */ 
class Button extends Element
{
	/*
	 * Constructor of the Button class
	 */
	public function Button($name, $value)
    {
       parent::Element($name, $value);
    }
    
    /*
     *
     * Return the HTML of the button     
     */
   	public function render()
    {
       	// return the button
    	$html = array();
    	$html[] = sprintf(
          	'<input type="button" value="%s" name="%s" id="%2$s" %s />',
    		$this->value,
    		$this->name,
    		$this->attributestorage->get_attributes());
    	return implode("\n", $html);
    }
}
?>