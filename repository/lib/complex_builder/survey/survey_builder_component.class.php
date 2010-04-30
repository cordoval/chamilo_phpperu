<?php
/**
 * $Id: survey_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.survey
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class SurveyBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Survey', $component_name, $builder);
    }

    function get_configure_context_url($selected_cloi)
    {
        return $this->get_parent()->get_configure_context_url($selected_cloi);
    }
	
	function get_browse_context_url($selected_cloi)
    {
        return $this->get_parent()->get_browse_context_url($selected_cloi);
    }
    
    
    
}

?>