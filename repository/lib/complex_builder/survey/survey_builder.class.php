<?php
/**
 * $Id: survey_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.survey
 */
require_once dirname(__FILE__) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = SurveyBuilderComponent :: factory('Browser', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>