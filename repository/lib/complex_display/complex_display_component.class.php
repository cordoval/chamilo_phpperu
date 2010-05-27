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

        $class = 'ComplexDisplay' . Utilities :: underscores_to_camelcase($type) . 'Component';
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
    }

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }
}
?>