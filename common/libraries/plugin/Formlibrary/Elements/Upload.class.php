<?php
/*
 * Class to create a upload field
 */
class Upload extends Element
{
	protected $target; //Path where the uploaded file has to be moved to
	
	public function Upload($name, $label, $target = null)
	{
		parent::Element($name, null, $label);
		$this->target = $target;		
	}

	/*
	 * This function returns the HTML of the created uploadfield
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf(
		'<input type="file" name="%s" id="%1$s" >',
		$this->name,
		$this->label);
		return implode('\n', $html);
	}
	
	/*
	 * This function checks if the uploaded file is valid
	 */
	public function is_valid()
	{
		if(!empty($_FILES[$this->name]))
		{
			$valid = true;
			foreach($this->get_rulestorage()->get_rules() as $rule)
			{
				$valid = $rule->control($_FILES[$this->name]['name']);
				if(!$valid)
				{
					$this->elementerror .= $rule->get_message() . '<br/>';
					$valid = false;
					break;
				}
			}
			return $valid;	
		}
		else return true;
	}
	
	public function get_file()
	{
		if(isset($_FILES[$this->name]))
		{
			return $_FILES[$this->name];
		}
	}
}
?>