<?php
/*
 * Class to create a textarea
 */
class TextArea extends Element 
{
	/*
	 * Constructor of the TextArea class
	 */
	public function TextArea($name, $value, $label)
    {
        // call the constructor of the Element class
        parent::Element($name,$value, $label);
    }
    
    /*
     * Returns the HTML of the created textarea
     */
    public function render()
    {        
        $val = "";
		if(isset($_POST[$this->get_name()])) 
			$val = htmlspecialchars($this->get_value());
		else $val = '';
		$html = array();
    	$html[] = sprintf(
          '<textarea name="%s" id="%1$s" %s>'. $val .'</textarea>',
          $this->name,
          $this->attributestorage->get_attributes()
          );
        return implode('\n', $html);
    }
}
?>