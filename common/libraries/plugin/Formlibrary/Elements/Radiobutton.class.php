<?php
/*
 * Class to create a radiobutton or a group of radiobuttons
 */
class RadioButton extends Element
{     
	protected $options; //Array that contains the different options for the radiobuttons
    
	/*
	 * Constructor to create a radiobutton
	 */
    public function RadioButton($name, $label, $options)
    {
        // call the constructor of the Element class
        parent::Element($name, null, $label);

        $this->options = $options;
    }    
   
    /*
     * Returns the HTML of the created radiobutton(s)
     */
 	public function render()
    {
		if(is_array($this->options) && count($this->options )>0)
        {
            $result = '';
            foreach($this->options as $key => $value)
            {               				
                $result .= $this->get_radiobutton($key, $value);
            }
        }        
        elseif($this->options == '' || count($this->options )===0)
        {
        	$result = ' '; 
        }        
        else
        {
            $result = $this->get_radiobutton($this->options, '');
        }
        return $result .'<span>'. $this->get_errors(). '</span>';
    }

    /*
     * RadioButton::get_radiobutton()
     *
     * Returns the radiobutton with the given title and value
     *     
     */
    public function get_radiobutton($value, $title)
    {        
    	static $counter = 1;

    	$value = trim($value);
    	$title = trim($title);

    	$html = array();
    	$html[] = sprintf(
          	'<input type="radio" name="%s" id="%1$s_%d" value="%s" %s'.'><label for="%1$s_%2$d" class="noStyle">%s</label>',
    		$this->name,
    		$counter++,
    		htmlspecialchars($value),
    		($value == $this->get_value()) ? "checked=\"checked\"" : "" .$this->attributestorage->get_attributes(),
    		$title);
    	return implode('\n', $html);
    }
}
?>