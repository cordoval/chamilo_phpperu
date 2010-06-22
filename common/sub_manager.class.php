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

    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

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

    abstract function run();

    abstract function get_application_component_path();
}
?>