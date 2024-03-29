<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\SubManager;

use Exception;

/**
 * $Id: complex_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder
 */
/**
 * This class represents a basic complex builder structure.
 * When a builder is needed for a certain type of complex learning object an extension should be written.
 * We will make use of the repoviewer for selection, creation of learning objects
 *
 * @author vanpouckesven
 *
 */
abstract class ComplexBuilderComponent extends SubManager
{
    const BROWSER_COMPONENT = 'browser';
    const CREATOR_COMPONENT = 'creator';
    const DELETER_COMPONENT = 'deleter';
    const MOVER_COMPONENT = 'mover';
    const PARENT_CHANGER_COMPONENT = 'parent_changer';
    const UPDATER_COMPONENT = 'updater';
    const VIEWER_COMPONENT = 'viewer';

    static function factory($type, $application)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ComplexbuilderComponentTypeDoesNotExist', array('TYPE' => $type)));
        }

        require_once $file;
        $class = __NAMESPACE__ . '\\' . 'ComplexBuilderComponent' . Utilities :: underscores_to_camelcase($type) . 'Component';

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

    function get_additional_links()
    {
        return $this->get_parent()->get_additional_links();
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

    function get_creation_links(ContentObject $content_object, $types = array(), $additional_links = array())
    {
        return $this->get_parent()->get_creation_links($content_object, $types, $additional_links);
    }

    function get_complex_content_object_item_view_url($complex_content_object_item, $root_content_object_id)
    {
        return $this->get_parent()->get_complex_content_object_item_view_url($complex_content_object_item, $root_content_object_id);
    }

    function get_complex_content_object_parent_changer_url($complex_content_object_item, $root_content_object_id)
    {
        return $this->get_complex_content_object_parent_changer_url($complex_content_object_item, $root_content_object_id);
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/complex_builder/component/';
    }

    function get_action_bar(ContentObject $content_object)
    {
        return $this->get_parent()->get_action_bar($content_object);
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
        return ComplexBuilder :: DEFAULT_ACTION;
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
        return ComplexBuilder :: PARAM_BUILDER_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application, false);
    }
}

?>