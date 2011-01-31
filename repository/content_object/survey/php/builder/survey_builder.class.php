<?php
namespace repository\content_object\survey;

use repository\ComplexBuilder;
use common\libraries\ComplexMenuSupport;

class SurveyBuilder extends ComplexBuilder implements ComplexMenuSupport
{
    
    const ACTION_CONFIGURE_PAGE = 'configure';
    const ACTION_CHANGE_QUESTION_VISIBILITY = 'visibility_changer';
    const ACTION_CONFIGURE_QUESTION = 'configure_question';
    const ACTION_DELETE_CONFIG = 'config_deleter';
    const ACTION_UPDATE_CONFIG = 'config_updater';
    
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_COMPLEX_QUESTION_ITEM_ID = 'complex_question_item_id';
    const PARAM_CONFIG_INDEX = 'config_index';

    function __construct($parent)
    {
        parent :: __construct($parent);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_configure_url($selected_cloi)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_PAGE, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(), self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_cloi, self :: PARAM_SURVEY_PAGE_ID => $selected_cloi->get_ref()));
    }

    function get_config_delete_url($config_index)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_DELETE_CONFIG, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(), self :: PARAM_CONFIG_INDEX => $config_index));
    }

    function get_config_update_url($config_index)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_UPDATE_CONFIG, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(), self :: PARAM_CONFIG_INDEX => $config_index));
    }

    function get_change_question_visibility_url($complex_question_item)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CHANGE_QUESTION_VISIBILITY, self :: PARAM_COMPLEX_QUESTION_ITEM_ID => $complex_question_item->get_id()));
    }

    function get_configure_question_url($complex_question_item)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_QUESTION, self :: PARAM_COMPLEX_QUESTION_ITEM_ID => $complex_question_item->get_id()));
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