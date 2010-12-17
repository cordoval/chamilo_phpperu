<?php
/*
 * Class to create a submitbutton
 */ 
class SubmitButton extends Element
{
	/*
	 * Constructor for a submitbutton
	 */
	public function SubmitButton($name, $value)
    {
       parent::Element($name, $value);
    }
    
    /*
     * This function returns the HTML of the created submitbutton     
     */
   	public function render()
    {
       	// return the button
        $html = array();
    	$html[] = sprintf(
          '<input type="submit" value="%s" name="%s" id="%2$s" %s />',
          $this->value,
          $this->name,
          '');          
        return implode('\n', $html);
    }
}
?>