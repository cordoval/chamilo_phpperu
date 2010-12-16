<?php
namespace common\libraries;
/**
 * $Id: core_application.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
abstract class CoreApplication extends BasicApplication
{
	const CLASS_NAME = __CLASS__;
    /**
     *
     * @see Application::is_active()
     */
    function is_active($application)
    {
    	return self :: exists($name);
    }

    /**
     * Determines if a given application exists
     * @param string $name
     * @return boolean
     * @deprecated
     */
    public static function is_application($name)
    {
    	return self :: exists($name);
    }

    public static function get_list()
    {
        $applications = array();
        $applications[] = 'admin';
        $applications[] = 'tracking';
        $applications[] = 'repository';
        $applications[] = 'user';
        $applications[] = 'group';
        $applications[] = 'rights';
        $applications[] = 'home';
        $applications[] = 'menu';
        $applications[] = 'webservice';
        $applications[] = 'reporting';

        return $applications;
    }

    /**
     * Gets a link to the personal calendar application
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: get_link($parameters, $filter, $encode_entities, $application_type);
    }

    /**
     * @see Application :: simple_redirect
     */
    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    /**
     * @see Application :: redirect
     */
    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: redirect($message, $error_message, $parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    public static function get_application_path($application_name)
    {
        return Path :: get(SYS_PATH) . $application_name . '/';
    }
    
	public static function get_application_web_path($application_name)
    {
    	return Path :: get(WEB_PATH) . $application_name . '/' ;
    }

    /**
     * @deprecated
     */
    public function get_application_component_path()
    {
        $application_name = $this->get_application_name();
        return $this->get_application_path($application_name) . 'lib/' . $application_name . '_manager/component/';
    }

    static function get_application_namespace($application_name)
    {
    	return $application_name;
    } 
    
    /**
     * @deprecated
     */
    static function factory($application, $user = null)
    {
        require_once self :: get_application_manager_path($application);
        $class = self :: get_application_namespace($application) . '\\' . self :: get_application_class_name($application);
        return new $class($user);
    }
    
    static function exists($application)
    {
    	$application_path = self :: get_application_path($application);
        
        if (file_exists($application_path) && is_dir($application_path) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>