<?php
/**
 * $Id: complex_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display
 */
/**
 * @author Michael Kyndt
 */
abstract class ComplexDisplayComponent
{
    private $parent;
    private static $component_count = 0;

    function ComplexDisplayComponent($parent)
    {
        $this->parent = $parent;
        $this->id = ++ self :: $component_count;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function get_action()
    {
        return $this->get_parent()->get_action();
    }

    function set_action($action)
    {
        $this->get_parent()->set_action($action);
    }

    function set_parameter($parameter, $value)
    {
        $this->get_parent()->set_parameter($parameter, $value);
    }

    function get_parameter($parammeter)
    {
        return $this->get_parent()->get_parameter($parameter);
    }

    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    function display_header($breadcrumbtrail)
    {
        $this->get_parent()->display_header($breadcrumbtrail);
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

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
    }

    function get_url($additional_parameters = array ())
    {
        return $this->get_parent()->get_url($additional_parameters);
    }

    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    function get_root_lo()
    {
        return $this->get_parent()->get_root_lo();
    }

    function get_cloi()
    {
        return $this->get_parent()->get_cloi();
    }

    static function factory($display_name, $component_name, $display)
    {
        $filename = dirname(__FILE__) . '/' . Utilities :: camelcase_to_underscores($display_name) . '/component/' . Utilities :: camelcase_to_underscores($component_name) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $component_name . '" component');
        }
        
        $class = $display_name . 'Display' . $component_name . 'Component';
        if (! $display_name)
            $class = 'Complex' . $class;
        
        require_once $filename;
        return new $class($display);
    }

    /**
     * Common functionality
     */
    
    function get_clo_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return $this->get_parent()->get_clo_table_html($show_subitems_column, $model, $renderer);
    }

    function get_clo_table_condition()
    {
        return $this->get_parent()->get_clo_table_condition();
    }

    function get_clo_menu()
    {
        return $this->get_parent()->get_clo_menu();
    }

    function get_clo_breadcrumbs()
    {
        return $this->get_parent()->get_clo_breadcrumbs();
    }

    function get_action_bar($lo)
    {
        return $this->get_parent()->get_action_bar($lo);
    }

    function get_creation_links($lo, $types = array())
    {
        return $this->get_parent()->get_creation_links($lo, $types);
    }

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }
}

?>