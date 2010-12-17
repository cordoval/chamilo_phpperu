<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Digit.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Alpha.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/AlphaNum.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Bool.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Email.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/File.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Float.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Integer.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/URL.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Maxlength.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Minlength.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Required.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Filetypes.class.php';

/*
 * Abstract class Rule, every specified rule inherits from this one 
 */
abstract class Rule
{
	protected $message; //string: contains the error message
	protected $script;
	
	/*
	 * Constructor for a rule
	 * It gets the error message as a parameter
	 */
	public function Rule($message)
	{
		$this->message = $message;				
	}
	
	/*
	 * This fucntion returns the error message
	 */
	public function get_message()
	{
		return $this->message;
	}
	
	public function get_script()
	{
		return $this->script;
	}
}
?>