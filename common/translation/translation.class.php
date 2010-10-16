<?php
/**
 * $Id: translation.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.translation
 */
class Translation
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Language strings defined in the language-files. Stored as an associative array.
     */
    private $strings;

    /**
     * The language we're currently translating too
     */
    private $language;

    /**
     * The application we're currently translating
     */
    private $application;

    /**
     * To determine wether we should show the variable in a tooltip window or not (used for translation purposes)
     */
    private $show_variable_in_translation;

    /**
     * Constructor.
     */
    private function Translation($language = null)
    {
        if (is_null($language))
        {
            global $language_interface;
            $this->language = $language_interface;
            if (Request :: get('install_running') != 1 && file_exists(dirname(__FILE__) . '/../configuration/configuration.php'))
            {
                $this->show_variable_in_translation = PlatformSetting :: get('show_variable_in_translation');
            }
        }
        else
        {
            $this->language = $language;
        }
        $this->strings = array();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * TODO: This comment does not fit here.
     * Returns the instance of this class.
     *
     * TODO: make this function static
     *
     * @param String $variable
     * @param Array $parameters (always use capital letters)
     *
     * Example:
     * Translation :: get('UserCount', array('COUNT' => $usercount));
     * $lang['user']['UserCount'] = There are {COUNT} users on the system;
     *
     * @return Translation The instance.
     */
    static function get($variable, $parameters = array())
    {
        $instance = self :: get_instance();
        $translation = $instance->translate($variable);

        if (empty($parameters))
        {
            return $translation;
        }
        else
        {
        	return preg_replace('#\{([A-Z0-9\-_]+)\}#e', 'isset($parameters[\'\\1\']) ? $parameters[\'\\1\'] : \'\'', $translation);
        }
    }

    function get_language()
    {
        $instance = self :: get_instance();
        return $instance->language;
    }

    function set_language($language)
    {
        $instance = self :: get_instance();
        $instance->language = $language;
    }

    function get_application()
    {
        $instance = self :: get_instance();
        return $instance->application;
    }

    function set_application($application)
    {
        $instance = self :: get_instance();
        $instance->application = $application;
    }

    /**
     * This comment does not fit here.
     * Gets a parameter from the configuration.
     * @param string $section The name of the section in which the parameter
     *                        is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    function translate($variable)
    {
        $instance = self :: get_instance();

        $language = $instance->language;
        //$strings = $instance->strings;
        $strings = & $instance->strings;
        //
        $value = '';

        if (! isset($strings[$language]))
        {
            $instance->add_language_file_to_array($language, 'common');
        }
        elseif (! isset($strings[$language]['common']))
        {
            $instance->add_language_file_to_array($language, 'common');
        }

        $application = $instance->get_application();

        if (! isset($application))
        {
            $application = 'common';
        }

        if (! isset($strings[$language][$application]))
        {
            $instance->add_language_file_to_array($language, $application);
        }

        // Removed by Ivan Tcholakov, 31-MAR-2010, see BUG #743
        //$strings = $instance->strings;

        if (isset($strings[$language][$application][$variable]))
        {
            $value = $strings[$language][$application][$variable];
        }
        elseif (isset($strings[$language]['common'][$variable]))
        {
            $value = $strings[$language]['common'][$variable];
        }
        else
        {
        	$packages = array('application_common', 'repository', 'components');

        	foreach($packages as $package)
        	{
	        	if(!isset($strings[$language][$package]))
	        	{
	        		$instance->add_language_file_to_array($language, $package);
	        	}

		        if (isset($strings[$language][$package][$variable]))
		        {
		            $value = $strings[$language][$package][$variable];
		            break;
		        }
        	}
        }

        if (!$value || $value == '' || $value == ' ')
        {
            if ( (Request :: get('install_running') != 1 && file_exists(dirname(__FILE__) . '/../configuration/configuration.php')) && PlatformSetting :: get('hide_dcda_markup'))
            {
                return $variable;
            }
            else
            {
                return '[=' . self :: application_to_class($application) . '=' . $variable . '=]';
            }
        }

        if ($this->show_variable_in_translation)
        {
            return '<span title="' . $application . ' - ' . $variable . '">' . $value . '</span>';
        }

        return $value;
    }

    function add_language_file_to_array($language, $application)
    {
        $lang = array();
        $path = Path :: get_language_path() . $language . '/' . $application . '.inc.php';
        include_once ($path);
        $instance = self :: get_instance();
        $instance->strings[$language][$application] = $lang[$application];
    }

    static function application_to_class($application)
    {
        return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
    }
}
?>