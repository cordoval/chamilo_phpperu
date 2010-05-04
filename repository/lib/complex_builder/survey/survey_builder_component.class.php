<?php

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

    function get_template_viewing_url($template_id)
    {
        return $this->get_parent()->get_template_viewing_url($template_id);
    }

    function get_template_suscribe_page_browser_url($template_id)
    {
        return $this->get_parent()->get_template_suscribe_page_browser_url($template_id);
    }

    function get_template_suscribe_page_url($template_id, $page_id)
    {
        return $this->get_parent()->get_template_suscribe_page_url($template_id, $page_id);
    }

    function get_template_unsubscribing_page_url($template_rel_page)
    {
        return $this->get_parent()->get_template_unsubscribing_page_url($template_rel_page);
    }

    function get_template_emptying_url($template_id)
    {
        return $this->get_parent()->get_template_emptying_url($template_id);
    }
}

?>