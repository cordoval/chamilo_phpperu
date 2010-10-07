<?php

require_once dirname(__FILE__) . '/browser/survey_browser_table_cell_renderer.class.php';

class SurveyBuilderBrowserComponent extends SurveyBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: BROWSER_COMPONENT, $this);
        
        $browser->run();
    }

    function get_action_bar($content_object)
    {
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
//        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('SubscribeSurveyContext'), Theme :: get_common_image_path() . 'action_build_prerequisites.png', $this->get_add_context_url($content_object)));
//        
//        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ConfigureSurveyContext'), Theme :: get_common_image_path() . 'action_build_prerequisites.png', $this->get_configure_context_url($content_object)));
        return $action_bar->as_html();
    }

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new SurveyBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_TEMPLATE_ID);
    }

}

?>