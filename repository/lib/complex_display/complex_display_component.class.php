<?php
/**
 * $Id: complex_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display
 */
/**
 * @author Michael Kyndt
 */
abstract class ComplexDisplayComponent extends SubManager
{
    const ATTACHMENT_VIEWER_COMPONENT = 'attachment_viewer';
    const COMPLEX_FEEDBACK_COMPONENT = 'creator';
    const CONTENT_OBJECT_UPDATER_COMPONENT = 'content_object_updater';
    const CREATOR_COMPONENT = 'creator';
    const DELETER_COMPONENT = 'deleter';
    const FEEDBACK_DELETER_COMPONENT = 'feedback_deleter';
    const FEEDBACK_EDITOR_COMPONENT = 'editor';
    const REPORTING_TEMPLATE_VIEWER_COMPONENT = 'reporting_template_viewer';
    const UPDATER_COMPONENT = 'updater';

    static function factory($type, $application)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ComplexDisplayComponentTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = 'ComplexDisplayComponent' . Utilities :: underscores_to_camelcase($type) . 'Component';
        return new $class($application);
    }

	function get_root_content_object()
    { 
    	return $this->get_parent()->get_root_content_object();
    }

    function get_complex_content_object_item()
    {
    	return $this->get_parent()->get_complex_content_object_item();
    }

	function get_selected_complex_content_object_item()
    {
    	return $this->get_parent()->get_selected_complex_content_object_item();
    }

	function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object_id();
    }

    function get_complex_content_object_item_id()
    {
    	return $this->get_parent()->get_complex_content_object_item_id();
    }

	function get_selected_complex_content_object_item_id()
    {
    	return $this->get_parent()->get_selected_complex_content_object_item_id();
    }

    /**
     * Common functionality
     */

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return $this->get_parent()->get_complex_content_object_table_html($show_subitems_column, $model, $renderer);
    }

    function get_complex_content_object_table_condition()
    {
        return $this->get_parent()->get_complex_content_object_table_condition();
    }

    function get_complex_content_object_menu()
    {
        return $this->get_parent()->get_complex_content_object_menu();
    }

    function get_complex_content_object_breadcrumbs()
    {
        return $this->get_parent()->get_complex_content_object_breadcrumbs();
    }

    function get_action_bar(ContentObject $content_object)
    {
        return $this->get_parent()->get_action_bar($content_object);
    }

	function get_application_component_path()
	{
		return Path :: get_repository_path() . 'lib/complex_display/component/';
	}

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }
    
    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return ComplexDisplay :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return ComplexDisplay :: PARAM_DISPLAY_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>