<?php
require_once ('plugin/FormLibrary/Elements/Element.class.php');

class StyleButton extends Element
{
	private $styleButtonLabel;
	private $type;	

    public function StyleButton($type, $elementName = null, $elementLabel = null, $value = null)
    {
        parent::Element($elementName, $value);
        $this->styleButtonLabel = $elementLabel;
		$this->type = $type;
        
        if (isset($value))
        {
        	$this->set_value($value);
        }
        else
        {
        	$this->set_value($elementName);          	      	
        }        
    }
    
    public function getStyleButtonLabel()
    {
    	return $this->styleButtonLabel;
    }  
   
    public function render()
    {
        return sprintf('<button name="' . $this->get_name() .'" " type="'. $this->type .'" value="'. $this->get_value() . '""%s" >' . $this->getStyleButtonLabel() . '</button>',
        				$this->attributestorage->get_attributes());
        
    }
}
?>