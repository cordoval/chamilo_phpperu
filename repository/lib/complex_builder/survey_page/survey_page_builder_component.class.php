<?php
/**
 * $Id: survey_page_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.survey_page
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class SurveyPageBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('SurveyPage', $component_name, $builder);
    }

    function get_routing_url($selected_cloi)
    {
        return $this->get_parent()->get_routing_url($selected_cloi);
    }

}

?>
