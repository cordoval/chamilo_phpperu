<?php
/*
 * Class for an imagebutton
 */
class ImageButton extends Element
{
    protected $source; //URL of the image
    
    /* 
     * Constructor for a imagebutton     
     */
	public function ImageButton($name, $source)
    {
        parent::Element($name);
        $this->source = $source;
    }

    /*     
     * This function returns the HTML of the created imagebutton     
     */
    public function render()
    {
        $html = array();
    	$html[] = sprintf(
          '<input type="image"  name="%s" src="%s" id="%2$s" %s />',
          $this->name,
          $this->source,
          $this->attributestorage->get_attributes());
        return implode('\n', $html);        
    }
}
?>