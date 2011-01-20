<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Label.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Html.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Text.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Password.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Image.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/TextArea.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/RadioButton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Checkbox.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Select.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Hidden.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Upload.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Button.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/ImageButton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/SubmitButton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/ResetButton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Link.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Rule.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Storages/RuleStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Storages/AttributeStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/StyleButton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/StyleSubmitbutton.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/StyleResetbutton.class.php';

/*
 * Abstract class for an element
 * Each element inherits from this class
 */
abstract class Element
{
	protected $name;
	protected $label;
	public $value;
	protected $attributestorage;
	protected $rulestorage;
	protected $elementerror;	
	
	/*
	 * Constructor for an element
	 */
	public function Element($name, $value= null, $label = null)
	{
		$this->name = $name;	
		$this->value = $value;	
		$this->label = $label;
		$this->attributestorage = new AttributeStorage();		
		$this->rulestorage = new RuleStorage();		
	}	
	
	/*
	 * Getter for the name of the element
	 */
	public function get_name()
	{
		return $this->name;
	}
	
	public function set_name($name)
	{
		if(!empty($name))
			$this->name = $name;
	}
	
	/*
	 * Getter for the label of the element
	 */
	public function get_label()
	{
		return $this->label;
	}
	
	/*
	 * Setter for the label of the element
	 */
	public function set_label($label)
	{
		if(!empty($label))
		{	$this->label = $label; }		
	}
	
	/*
	 * Get the attribute storage
	 */
	public function get_attributestorage()
	{
		return $this->attributestorage;
	}
	
	/*
	 * Function to get the value of an element
	 */
	public function get_value()
	{
		if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];
		}
		else return $this->value;
	}
	
	/*
	 * function to set the value of an element
	 */
	public function set_value($value)
	{
		if(!empty($value))
			$this->value = $value;
	}	
	
	/*
	 * Getter for the rulestorage of the element
	 */
	public function get_rulestorage()
	{
		return $this->rulestorage;
	}	

	/*
	 * This function returns the errors that occur when a rule isn't respected
	 */
	public function get_errors()
	{
		return $this->elementerror;
	}

	/*
	 * Check if value of the element is valid
	 */
	public function is_valid()
	{
		$valid = true;
		foreach($this->get_rulestorage()->get_rules() as $rule)
		{
			$valid = $rule->control($this->get_value());
			if(!$valid)
			{
				$this->elementerror .= $rule->get_message() . '<br/>';
				$valid = false;
				break;
			}
		}
		return $valid;		
	}		
}
?>