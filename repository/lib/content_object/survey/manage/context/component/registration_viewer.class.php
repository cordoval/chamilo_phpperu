<?php

require_once dirname(__FILE__) . '/context_table/table.class.php';

class SurveyContextManagerRegistrationViewerComponent extends SurveyContextManager
{
    private $ab;
    private $context_registration;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
    	        
        $this->context_registration = SurveyContextDataManager::get_instance()->retrieve_survey_context_registration($context_registration_id);
    	        
        $trail = BreadcrumbTrail :: get_instance();
        
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
        
        $table = new SurveyContextTable($this, $parameters, $this->get_condition(), $this->context_registration);
        
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
            $conditions[] = new PatternMatchCondition(SurveyContext :: PROPERTY_NAME, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
//            $conditions[] = new PatternMatchCondition(SurveyContextRegistration :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            $condition = new OrCondition($conditions);
        
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
              
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_creation_url($this->context_registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        return $action_bar;
    }
}
?>