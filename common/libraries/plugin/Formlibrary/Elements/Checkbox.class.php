<?php
/*
 * Class to create a checkbox or group of checkboxes
 */
class CheckBox extends Element
{
	protected $options; // Array: contains all the options

	/*
     * CheckBox::CheckBox()     
     * Constructor: Create a new checkbox object      
     */
	public function CheckBox($name, $label, $options )
	{
		$this->options = $options;

		// call the constructor of the Field class
		parent::Element($name, null, $label);
	}
	
	/*
	 * This function returns all the options that were submitted by the user 
	 */
	public function get_options()
	{
		return $this->options;
	}
	
	/*
	 * Function to get the values of the checked checkboxes
	 */
	public function get_values()
	{		
		$temp = array();
		if(is_array($this->get_value()))
		{
			foreach ($this->get_value() as $val)
			{
				array_push($temp, $val);
			}
		}
		else array_push($temp, $this->get_value());
		return $temp;
	}
	
	/*
	 * This function returns the HTML of the created checkbox(es)
	 */
	public function render()
	{
		// If there are multiple checkboxes
		if(is_array($this->options ) && count( $this->options )>0 )
		{
			$result = '';

			// get the checkboxes
			foreach($this->options as $key => $value )
			{
				// get the checkbox
				$result .= $this->get_checkbox($key, $value);
			}
		}
		elseif(is_array($this->options) && count($this->options) === 0)
		{
			$result = '';
		}

		// just 1 checkbox
		else
		{
			$result = $this->get_checkbox($this->options, '' );
		}
		return $result;
	}

	/*
     * Return an option of the checkbox with the given value
     */
	public function get_checkbox($value, $title)
	{
		static $counter = 1;
		static $counter1 = 1;		

		// remove unwanted spaces
		$sValue = trim($value );
		$sTitle = trim($title );

		// get the field HTML
		if( $title == '' )
		{
			$html[] = sprintf(
				'<input type="checkbox" name="%s" id="%s_%d" value="%s" %s'.'>',
				$this->name.(is_array($this->options)?'['. $counter1++ .']':''),			
				$this->name,
				$counter++,
				htmlspecialchars($value),
				(in_array($value, $this->get_values())) ? "checked=\"checked\"" : "" .$this->attributestorage->get_attributes(),
				$title
				);
		}
		else
		{
			$html[] = sprintf(
				'<input type="checkbox" name="%s" id="%s_%d" value="%s" %s'.'><label for="%2$s_%3$d" class="noStyle">%s</label>',
				$this->name.(is_array($this->options)?'['. $counter1++ .']':''),
				$this->name,
				$counter++,
				htmlspecialchars($value),
				(in_array($value, $this->get_values())) ? "checked=\"checked\"" : "". $this->attributestorage->get_attributes(),
				$title
				);			
		}
		return implode("\n", $html);				
	}		
}
?>