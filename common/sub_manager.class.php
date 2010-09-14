<?php
/**
 * $Id: sub_manager.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
abstract class SubManager
{
    private $parent;

    function SubManager($parent)
    {
        $this->parent = $parent;

        if (Request :: get(Application :: PARAM_APPLICATION) == $this->parent->get_application_name())
        {
            $this->parent->handle_table_action();
        }
    }

    function handle_table_action()
    {
        $this->parent->handle_table_action();
    }

    function get_parent()
    {
        return $this->parent;
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        return $this->get_parent()->get_link($parameters, $filter, $encode_entities, $application_type);
    }

    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_APPLICATION)
    {
        $this->get_parent()->simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_APPLICATION)
    {
        $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    function set_parameter($name, $value)
    {
        $this->get_parent()->set_parameter($name, $value);
    }

    function set_breadcrumbs($breadcrumbs)
    {
        $this->get_parent()->set_breadcrumbs($breadcrumbs);
    }

    function get_breadcrumbs()
    {
        return $this->get_parent()->get_breadcrumbs();
    }

    function display_portal_header()
    {
        $this->get_parent()->display_portal_header();
    }

    function display_portal_footer()
    {
        $this->get_parent()->display_portal_footer();
    }

    function display_header($breadcrumbtrail = null, $display_title = true)
    {
        $this->get_parent()->display_header($breadcrumbtrail, $display_title);
    }

    function display_footer()
    {
        $this->get_parent()->display_footer();
    }

    function display_message($message)
    {
        $this->get_parent()->display_message($message);
    }

    function display_error_message($message)
    {
        $this->get_parent()->display_error_message($message);
    }

    function display_warning_message($message)
    {
        $this->get_parent()->display_warning_message($message);
    }

    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, $succes_message_multiple)
    {
        return $this->get_parent()->get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, $succes_message_multiple);
    }

    function not_allowed()
    {
        $this->get_parent()->not_allowed();
    }

    /**
     * @return int
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    /**
     * @return User
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    function get_action()
    {
        return $this->get_parent()->get_action();
    }

    function set_action($action)
    {
        return $this->get_parent()->set_action($action);
    }

    function get_platform_setting($variable)
    {
        return $this->get_parent()->get_platform_setting($variable);
    }

    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    function get_application_name()
    {
        return $this->get_parent()->get_application_name();
    }

    function create_component($type, $application = null)
    {
        if ($application == null)
        {
            $application = $this;
        }

        return $this->get_parent()->create_component($type, $application);
    }

    //abstract function run();


    abstract function get_application_component_path();

    /**
     * EXPERIMENTAL ENHANCEMENTS
     * @author Hans De Bisschop
     */

    /**
     * @return string|NULL
     */
    function process_table_action($action_parameter)
    {
    	$table_name = Request :: post('table_name');
        if (isset($table_name))
        {
            $class = Utilities :: underscores_to_camelcase($table_name);
            if (class_exists($class))
            {
                call_user_func(array($class, 'handle_table_action'));

                $table_action_name = Request :: post($table_name . '_action_name');
                $table_action_value = Request :: post($table_name . '_action_value');
                Request :: set_get($table_action_name, $table_action_value);

                if ($table_action_name == $action_parameter)
                {
                    return $table_action_value;
                }
            }
        }

        return null;
    }

    /**
     * @param string $sub_manager_class
     * @return string
     */
    static function get_component_path($sub_manager_class)
    {
        return call_user_func(array($sub_manager_class, 'get_application_component_path'));
    }

    /**
     * @param string $sub_manager_class
     * @param string $action
     * @return string
     */
    private static function load_class($sub_manager_class, $action)
    {
        $application_component_path = self :: get_component_path($sub_manager_class);

        $file = $application_component_path . Utilities :: camelcase_to_underscores($action) . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . $sub_manager_class . '</li>';
            $message[] = '<li>' . $action . '</li>';
            $message[] = '</ul>';

            BreadcrumbTrail :: get_instance()->add(new Breadcrumb('#', Translation :: get($sub_manager_class)));

            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }

        require_once $file;

        return $sub_manager_class . Utilities :: underscores_to_camelcase($action) . 'Component';
    }

    /**
     * @param string $sub_manager_class
     * @param string $action
     * @param Application $application
     * @return SubManager
     */
    private static function component($sub_manager_class, $action, $application)
    {
        $class = self :: load_class($sub_manager_class, $action);
        return new $class($application);
    }

    /**
     * @param string $sub_manager_class
     * @param string $action_parameter
     * @return string
     */
    static function get_component_action($sub_manager_class, $action_parameter)
    {
        $default_action = call_user_func(array($sub_manager_class, 'get_default_action'));

        $action = Request :: get($action_parameter);
        $action = ! isset($action) ? $default_action : $action;
        $table_action = self :: process_table_action($action_parameter);
        if ($table_action)
        {
            $action = $table_action;
        }

        return $action;
    }

    /**
     * @param string $sub_manager_class
     * @param Application $application
     */
    static function construct($sub_manager_class, $application, $add_breadcrumb = true)
    {
        $action_parameter = call_user_func(array($sub_manager_class, 'get_action_parameter'));
        $action = self :: get_component_action($sub_manager_class, $action_parameter);

        $component = self :: component($sub_manager_class, $action, $application);
        $component->set_parameter($action_parameter, $action);
        if ($add_breadcrumb)
        {
            BreadcrumbTrail :: get_instance()->add(new Breadcrumb($component->get_url(), Translation :: get(get_class($component))));
        }

        return $component;
    }

    /**
     * @param string $sub_manager_class
     * @param Application $application
     */
    static function launch($sub_manager_class, $application, $add_breadcrumb = true)
    {
        self :: construct($sub_manager_class, $application, $add_breadcrumb)->run();
    }
}
?>