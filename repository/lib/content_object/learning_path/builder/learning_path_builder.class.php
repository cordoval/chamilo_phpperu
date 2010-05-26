<?php
/**
 * $Id: learning_path_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path
 */
//require_once dirname(__FILE__) . '/learning_path_builder_component.class.php';

class LearningPathBuilder extends ComplexBuilder
{
    const ACTION_CREATE_LP_ITEM = 'create_item';
    const ACTION_BUILD_PREREQUISITES = 'prerequisites';
    const ACTION_SET_MASTERY_SCORE = 'mastery_score';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create->component('Browser');
                break;
            case LearningPathBuilder :: ACTION_CREATE_LP_ITEM :
                $component = $this->create->component('ItemCreator');
                break;
            case self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create->component('Deleter');
                break;
            case self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create->component('Updater');
                break;
            case self :: ACTION_BUILD_PREREQUISITES :
                $component = $this->create->component('PrerequisitesBuilder');
                break;
            case self :: ACTION_SET_MASTERY_SCORE :
                $component = $this->create->component('MasteryScoreSetter');
                break;
            default:
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
                $component = $this->create_component('Browser');
                break;
        }
        
    }

    function get_prerequisites_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_PREREQUISITES, self :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID_ID => $selected_complex_content_object_item, 'publish' => Request :: get('publish')));
    }

    function get_mastery_score_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SET_MASTERY_SCORE, self :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item, 'publish' => Request :: get('publish')));
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

}

?>