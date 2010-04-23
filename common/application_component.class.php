<?php
/**
 * $Id: application_component.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
class ApplicationComponent
{
    /**
     * The application manager in which this component is used
     */
    private $manager;
    
    /**
     * The number of components allready instantiated
     */
    private static $component_count = 0;
    
    /**
     * The id of the component
     */
    private $id;

    /**
     * The ApplicationComponent constructor
     */
    function ApplicationComponent($manager)
    {
        $this->manager = $manager;
        $this->id = ++ self :: $component_count;
    }

    /**
     * @return String $id The application component id
     */
    function get_component_id()
    {
        return $this->id;
    }

    /**
     * @return Application $manager The web application
     */
    function get_parent()
    {
        return $this->manager;
    }

    /**
     * @see Application :: simple_redirect()
     */
    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
    {
        return $this->get_parent()->simple_redirect($parameters, $filter, $encode_entities, $type);
    }

    /**
     * @see Application :: redirect()
     */
    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
    {
        return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities, $type);
    }

    /**
     * @see Application :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see Application :: get_parameters()
     */
    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    /**
     * @see Application :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        return $this->get_parent()->set_parameter($name, $value);
    }
    
	function set_parameters($parameters)
    {
        return $this->get_parent()->set_parameters($parameters);
    }

    /**
     * @see Application :: get_url()
     */
    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @see Application :: get_link()
     */
    function get_link($parameters = array (), $filter = array(), $encode = false)
    {
        return $this->get_parent()->get_link($parameters, $filter, $encode);
    }

    /**
     * @see Application :: display_header()
     */
    function display_header($breadcrumbs = array ())
    {
        return $this->get_parent()->display_header($breadcrumbs);
    }

    /**
     * @see Application :: display_portal_header()
     */
    function display_portal_header()
    {
        return $this->get_parent()->display_portal_header();
    }

    /**
     * @see Application :: display_message()
     */
    function display_message($message)
    {
        return $this->get_parent()->display_message($message);
    }

    /**
     * @see Application :: display_error_message()
     */
    function display_error_message($message)
    {
        return $this->get_parent()->display_error_message($message);
    }

    /**
     * @see Application :: display_warning_message()
     */
    function display_warning_message($message)
    {
        return $this->get_parent()->display_warning_message($message);
    }

    /**
     * @see Application :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }

    /**
     * @see Application :: display_portal_footer()
     */
    function display_portal_footer()
    {
        return $this->get_parent()->display_portal_footer();
    }

    /**
     * @see Application :: display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see Application :: display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see Application :: display_popup_form
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * @see Application :: not_allowed()
     */
    function not_allowed($trail = null, $show_login_form = true)
    {
        $this->get_parent()->not_allowed($trail, $show_login_form);
    }

    /**
     * @see Application :: get_path
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * @see Application :: get_platform_setting
     */
    function get_platform_setting($variable)
    {
        return $this->get_parent()->get_platform_setting($variable);
    }

    /**
     * @see Application :: get_user()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see Application :: get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    /**
     * @see Application :: get_application_platform_admin_links()
     */
    function get_application_platform_admin_links()
    {
        return $this->get_parent()->get_application_platform_admin_links();
    }
    
	/**
     * @see Application :: get_application_platform_import_links()
     */
    function get_application_platform_import_links()
    {
        return $this->get_parent()->get_application_platform_import_links();
    }
    
    /**
     * Create a new application component
     * @param string $type The type of the component to create.
     * @param Application $manager The application in
     * which the created component will be used
     */
    static function factory($type, $manager)
    {
        $manager_class = get_class($manager);
        
        $file = $manager->get_application_component_path() . Utilities :: camelcase_to_underscores($type) . '.class.php';
        
        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($manager_class) . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';
            
            $application_name = Application :: application_to_class($manager->get_application_name());
            
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb('#', Translation :: get($application_name)));
            
            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        
        $class = $manager_class . $type . 'Component';
        require_once $file;
        return new $class($manager);
    }
}
?>