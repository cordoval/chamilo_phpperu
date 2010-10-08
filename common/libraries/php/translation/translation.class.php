<?php
/**
 * $Id: translation.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.translation
 */
class Translation
{
    const PACKAGE_DELIMITER = '.';
    const PACKAGE_COMMON = 'common';

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
     * @param String $variable
     * @param Array $parameters (always use capital letters)
     *
     * Example:
     * Translation :: get('UserCount', array('COUNT' => $usercount));
     * $lang['user']['UserCount'] = There are {COUNT} users on the system;
     *
     * @return Translation The instance.
     */
    function get($variable, $context, $parameters = array())
    {
        $called_class = get_called_class();
        //dump($called_class);
        $reflection_class = new ReflectionClass($called_class);
        //dump($reflection_class->getNamespaceName());
        $instance = self :: get_instance();
        $translation = $instance->translate($variable, $context);

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
     * is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    function translate($variable, $context)
    {
        $instance = self :: get_instance();

        $language = $instance->language;
        // Modified by Ivan Tcholakov, 31-MAR-2010, see BUG #743
        //$strings = $instance->strings;
        $strings = & $instance->strings;
        //
        $value = '';

        if (! isset($strings[$language]))
        {
            $instance->add_context_internationalization($language, $context);
        }
        elseif (! isset($strings[$language][$context]))
        {
            $instance->add_context_internationalization($language, $context);
        }

        if (isset($strings[$language][$context][$variable]))
        {
            $value = $strings[$language][$context][$variable];
        }

        if (! $value || $value == '' || $value == ' ')
        {
            if ((Request :: get('install_running') != 1 && file_exists(dirname(__FILE__) . '/../../configuration/configuration.php')) && PlatformSetting :: get('hide_dcda_markup'))
            {
                return $variable;
            }
            else
            {
                return '[=' . $context . '=' . $variable . '=]';
            }
        }

        if ($this->show_variable_in_translation)
        {
            return '<span title="' . $context . ' - ' . $variable . '">' . $value . '</span>';
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

    function add_context_internationalization($language, $context)
    {
        $path = BasicApplication :: get_application_resources_i18n_path($context) . $language . '.i18n';
        $strings = parse_ini_file($path);

        $instance = self :: get_instance();
        $instance->strings[$language][$context] = $strings;
    }

    static function application_to_class($application)
    {
        return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
    }
}
?>