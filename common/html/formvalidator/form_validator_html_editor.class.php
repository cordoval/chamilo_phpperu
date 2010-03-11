<?php
require_once Path :: get_library_path() . 'html/formvalidator/form_validator_html_editor_options.class.php';

class FormValidatorHtmlEditor
{
    private $form;
    private $name;
    private $label;
    private $required;
    private $attributes;
    private $options;

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

    function __construct($name, $label, $required = true, $options = array(), $attributes = array())
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;

        if (!array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'html_editor';
        }

        $this->attributes = $attributes;
    }

    function add()
    {
        $form = $this->get_form();
        $element = $this->create();

        $form->addElement($element);
        $form->applyFilter($this->get_name(), 'trim');

        if ($this->get_required())
        {
            $form->addRule($this->get_name(), Translation :: get('ThisFieldIsRequired'), 'required');
        }
    }

    function create()
    {
        $form = $this->get_form();
        $form->register_html_editor($this->name);
        return $form->createElement('textarea', $this->name, $this->label, $this->attributes);
    }

    function render()
    {
        return FormValidator :: createElement('textarea', $this->name, $this->label, $this->attributes)->toHtml();
    }

    function get_form()
    {
        return $this->form;
    }

    function set_form($form)
    {
        $this->form = $form;
    }

    function get_name()
    {
        return $this->name;
    }

    function set_name($name)
    {
        $this->name = $name;
    }

    function get_label()
    {
        return $this->label;
    }

    function set_label($label)
    {
        $this->label = $label;
    }

    function get_attributes()
    {
        return $this->attributes;
    }

    function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    function get_options()
    {
        return $this->options;
    }

    function set_options($options)
    {
        $this->options = $options;
    }

    function get_option($variable)
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

    function set_option($variable, $value)
    {
    	$this->options[$variable] = $value;
    }

    function get_required()
    {
        return $this->required;
    }

    function set_required($required)
    {
        $this->required = $required;
    }

    public static function factory($type, $name, $label, $required = true, $options = array(), $attributes = array())
    {
        $file = dirname(__FILE__) . '/html_editor/' . $type . '_html_editor.class.php';
        $class = 'FormValidator' . Utilities :: underscores_to_camelcase($type) . 'HtmlEditor';

        if (file_exists($file))
        {
            $options = FormValidatorHtmlEditorOptions :: factory($type, $options);

            if ($options)
            {
                require_once ($file);
                return new $class($name, $label, $required, $options, $attributes);
            }
        }
    }
}
?>