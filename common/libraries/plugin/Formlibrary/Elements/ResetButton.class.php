<?php
/*
 * Class for a resetbutton
 */
class ResetButton extends Element
{
    /* 
     * Constructor for a resetbutton     
     */
	public function ResetButton($name, $value)
    {
        parent::Element($name, $value);
    }

    /*     
     * This function returns the HTML of the created resetbutton     
     */
    public function render()
    {
        $html = array();
    	$html[] = sprintf(
          '<input type="reset" value="%s" name="%s" id="%2$s" %s />',
          $this->value,
          $this->name,
          $this->attributestorage->get_attributes());
        return implode('\n', $html);        
    }
}
?>