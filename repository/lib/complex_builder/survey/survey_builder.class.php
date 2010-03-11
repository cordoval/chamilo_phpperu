<?php
/**
 * $Id: survey_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.survey
 */
require_once dirname(__FILE__) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder
{
    
    const ACTION_CREATE_SURVEY_ITEM = 'create_item';
    const ACTION_BUILD_ROUTING = 'routing';

    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = SurveyBuilderComponent :: factory('Browser', $this);
                break;
            case SurveyBuilder :: ACTION_CREATE_SURVEY_ITEM :
                $component = SurveyBuilderComponent :: factory('ItemCreator', $this);
                break;
        }

        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function get_routing_url($selected_cloi)
    {
        $cloi_id = ($this->get_cloi()) ? ($this->get_cloi()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_ROUTING, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_SELECTED_CLOI_ID => $selected_cloi, 'publish' => Request :: get('publish')));
    }

}

?>