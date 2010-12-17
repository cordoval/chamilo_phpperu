<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Html_editor/form_library_html_editor_options.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Storages/AttributeStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Html_editor/tinymce_html_editor.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Html_editor/ckeditor_html_editor.class.php';

class FormLibraryHtmlEditor
{
    protected $name; 
    protected $label;
    protected $rulestorage;
    protected $attributestorage;
    protected $options;
    protected $elementerror;
    protected $value;

	const SETTING_TOOLBAR			= 'toolbar';
	const SETTING_LANGUAGE			= 'language';
	const SETTING_THEME				= 'theme';
	const SETTING_WIDTH				= 'width';
	const SETTING_HEIGHT			= 'height';
	const SETTING_COLLAPSE_TOOLBAR	= 'collapse_toolbar';
	const SETTING_CONFIGURATION		= 'configuration';
	const SETTING_FULL_PAGE			= 'full_page';
	const SETTING_ENTER_MODE		= 'enter_mode';
	const SETTING_SHIFT_ENTER_MODE	= 'shift_enter_mode';
	const SETTING_TEMPLATES			= 'templates';

    public function __construct($name, $label, $required = true, $options = array())
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;
    	$this->attributestorage = new AttributeStorage();
        $this->rulestorage = new RuleStorage($this->form);
		$at = new AttributeClass("html_editor");
        $this->attributestorage->add_attribute($at);
        if($required == true)
        {
        	$req = new Required($this);
        	$this->rulestorage->add_rule($req);
        }        
    }
    
    public function render()
    {}    

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_label()
    {
        return $this->label;
    }

    public function set_label($label)
    {
        $this->label = $label;
    }	

    public function set_value($value)
    {
        $this->value = $value;
    }
    
    public function get_options()
    {
        return $this->options;
    }

    public function set_options($options)
    {
        $this->options = $options;
    }

    public function get_option($variable)
    {
    	if (isset($this->options[$variable]))
    	{
    		return $this->options[$variable];
    	}
    	else
    	{
    		return null;
    	}
    }

    public function set_option($variable, $value)
    {
    	$this->options[$variable] = $value;
    }   
    
	/*
	 * Getter for the rulestorage of the editor
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

	public function get_value()
    {
    	if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];
		}
	}
}
?>