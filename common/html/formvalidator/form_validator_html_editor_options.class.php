<?php
/**
 * The combination of options available for the FormValidatorHtmlEditor
 * Should be implemented for each specific editor to translate the generic option values
 *
 * @author Scaramanga
 */

abstract class FormValidatorHtmlEditorOptions
{
    /**
     * @var Array The array containing all the options
     */
    private $options;

    /**
     * The name of the toolbar set e.g. Basic, Wiki, Assessment
     */
	const OPTION_TOOLBAR			= 'toolbar';

	/**
	 * Name of the language to be used for the editor
	 */
	const OPTION_LANGUAGE			= 'language';

	/**
	 * Name of the theme to be used for the editor
	 */
	const OPTION_THEME				= 'theme';

	/**
	 * The width of the editor in pixels or percent
	 */
	const OPTION_WIDTH				= 'width';

	/**
	 * The height of the editor in pixels
	 */
	const OPTION_HEIGHT				= 'height';

	/**
	 * Whether or not the toolbar should be collapse by default
	 */
	const OPTION_COLLAPSE_TOOLBAR	= 'collapse_toolbar';

	/**
	 * Path to the editors configuration file
	 */
	const OPTION_CONFIGURATION		= 'configuration';

	/**
	 * Whether or not the content of the editor should be treated as a standalone page
	 */
	const OPTION_FULL_PAGE			= 'full_page';

	/**
	 * Path to available templates for the editor
	 */
	const OPTION_TEMPLATES			= 'templates';

    /**
     * @param Array $options
     */
    function __construct($options)
    {
    	$this->options = $options;
    }

    /**
     * Returns the names of all available options
     *
     * @return Array The option names
     */
    function get_option_names()
    {
    	return array(self :: OPTION_COLLAPSE_TOOLBAR, self :: OPTION_CONFIGURATION, self :: OPTION_FULL_PAGE, self :: OPTION_HEIGHT, self :: OPTION_LANGUAGE, self :: OPTION_TEMPLATES, self :: OPTION_THEME, self :: OPTION_TOOLBAR, self :: OPTION_WIDTH);
    }

    /**
     * Gets all options
     *
     * @return Array The options
     */
    function get_options()
    {
        return $this->options;
    }

    /**
     * Set the options
     *
     * @param Array $options
     */
    function set_options($options)
    {
        $this->options = $options;
    }

    /**
     * Get a specific option's value or null if the option isn't set
     *
     * @return mixed the option's value
     */
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

    /**
     * Sets a specific option
     *
     * @param String $variable
     * @param mixed $value
     */
    function set_option($variable, $value)
    {
    	$this->options[$variable] = $value;
    }

    /**
     * Process the generic options into editor specific ones
     */
    abstract function render_options();

    function set_defaults()
    {
    	$available_options = $this->get_option_names();

    	foreach($available_options as $available_option)
    	{
    		$value = $this->get_option($available_option);
    		if (!isset($value))
    		{
    			switch($available_option)
    			{
    				case self :: OPTION_THEME :
    					$this->set_option($available_option, Theme :: get_theme());
    					break;
    				case self :: OPTION_LANGUAGE :
    					global $language_interface;
				        $editor_lang = AdminDataManager :: get_instance()->retrieve_language_from_english_name($language_interface)->get_isocode();
    					$this->set_option($available_option, $editor_lang);
    					break;

    				case self :: OPTION_TOOLBAR :
    					$this->set_option($available_option, 'Basic');
    					break;
    				case self :: OPTION_COLLAPSE_TOOLBAR :
    					$this->set_option($available_option, false);
    					break;

    				case self :: OPTION_WIDTH :
    					$this->set_option($available_option, '100%');
    					break;
    				case self :: OPTION_HEIGHT :
    					$this->set_option($available_option, '200');
    					break;

    				case self :: OPTION_FULL_PAGE :
    					$this->set_option($available_option, false);
    					break;
    			}
    		}
    	}
    }

    /**
     * @param String $type
     * @param Array $options
     * @return FormValidatorHtmlEditorOptions
     */
    public static function factory($type, $options = array())
    {
        $file = dirname(__FILE__) . '/html_editor_options/' . $type . '_html_editor_options.class.php';
        $class = 'FormValidator' . Utilities :: underscores_to_camelcase($type) . 'HtmlEditorOptions';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class($options);
        }
    }
}
?>