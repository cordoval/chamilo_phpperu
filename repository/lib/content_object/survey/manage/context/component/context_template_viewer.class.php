<?php

require_once dirname(__FILE__) . '/template_table/table.class.php';
require_once dirname(__FILE__) . '/survey_table/table.class.php';

class SurveyContextManagerContextTemplateViewerComponent extends SurveyContextManager
{
    
    const TAB_SURVEYS = 'surveys';
    const TAB_TEMPLATES = 'templates';
    
    private $ab;
    private $context_template;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_template_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID);
        $this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID, $context_template_id);
        $this->context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        
        $this->ab = $this->get_action_bar();
        
        $output = $this->get_tabs();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[self :: PARAM_CONTEXT_TEMPLATE_ID] = $this->context_template->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_SURVEYS;
        $table = new SurveyTable($this, $parameters, $this->get_survey_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SURVEYS, Translation :: get('Surveys'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_TEMPLATES;
        $table = new SurveyTemplateTable($this, $parameters, $this->get_condition(), $this->context_template);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_TEMPLATES, Translation :: get('Templates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            //            $conditions[] = new PatternMatchCondition(SurveyTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //            $conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //           $condition = new OrCondition($conditions);
        

        }
        
        return $condition;
    }

    function get_survey_condition()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id(), ContentObject :: get_table_name());
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Survey :: get_type_name(), ContentObject :: get_table_name());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', ContentObject :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', ContentObject :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_add.png', $this->get_template_creation_url($this->context_template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>