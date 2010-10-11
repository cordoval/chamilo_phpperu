<?php

require_once dirname(__FILE__) . '/template_table/table.class.php';

class SurveyContextManagerContextTemplateViewerComponent extends SurveyContextManager
{
    private $ab;
    private $context_template;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_template_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID);
    	$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID,$context_template_id);        
        $this->context_template = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($context_template_id);
    	        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->truncate();
        
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseContext')));
        $this->ab = $this->get_action_bar();
        
        $output = $this->get_browser_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        $table = new SurveyTemplateTable($this, $parameters, $this->get_condition(), $this->context_template);
        
        $html = array();
        $html[] = $table->as_html();
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(SurveyContext :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
//            $conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
            $condition = new OrCondition($conditions);
        
        }
        
        return $condition;
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