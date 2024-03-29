<?php
namespace repository\content_object\forum;

use common\libraries\ObjectTable;

use common\libraries\Request;
use repository\ComplexBuilder;
/**
 * $Id: forum_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum
 */

class ForumBuilder extends ComplexBuilder
{
    const ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM = 'sticky';
    const ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM = 'important';

    function __construct($parent)
    {
        $action = Request :: post('action');
        $_POST['action'] = null;
        parent :: __construct($parent);
    }

    function get_complex_content_object_item_sticky_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
    }

    function get_complex_content_object_item_important_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
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
        return self :: DEFAULT_ACTION;
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
        return self :: PARAM_BUILDER_ACTION;
    }
}

?>