<?php
/**
 * $Id: learning_path_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path
 */
require_once dirname(__FILE__) . '/learning_path_builder_component.class.php';

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
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = LearningPathBuilderComponent :: factory('Browser', $this);
                break;
            case LearningPathBuilder :: ACTION_CREATE_LP_ITEM :
                $component = LearningPathBuilderComponent :: factory('ItemCreator', $this);
                break;
            case self :: ACTION_DELETE_CLOI :
                $component = LearningPathBuilderComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_UPDATE_CLOI :
                $component = LearningPathBuilderComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_BUILD_PREREQUISITES :
                $component = LearningPathBuilderComponent :: factory('PrerequisitesBuilder', $this);
                break;
            case self :: ACTION_SET_MASTERY_SCORE :
                $component = LearningPathBuilderComponent :: factory('MasteryScoreSetter', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function get_prerequisites_url($selected_cloi)
    {
        $cloi_id = ($this->get_cloi()) ? ($this->get_cloi()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_PREREQUISITES, self :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_SELECTED_CLOI_ID => $selected_cloi, 'publish' => Request :: get('publish')));
    }

    function get_mastery_score_url($selected_cloi)
    {
        $cloi_id = ($this->get_cloi()) ? ($this->get_cloi()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SET_MASTERY_SCORE, self :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_SELECTED_CLOI_ID => $selected_cloi, 'publish' => Request :: get('publish')));
    }
}

?>