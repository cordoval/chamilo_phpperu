<?php

require_once dirname ( __FILE__ ) . '/context_template_subscribe_page_browser/subscribe_page_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey_page/survey_page.class.php';


class SurveyBuilderContextTemplateSubscribePageBrowserComponent extends SurveyBuilderComponent
{
    private $template;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
       
        $trail->add(new Breadcrumb($this->get_configure_context_url(), Translation :: get('BrowseContexts')));
        
        $survey_context_template_id = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_ID);
        $this->template = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($survey_context_template_id);
           
        
        $trail->add(new Breadcrumb($this->get_template_viewing_url($this->template), $this->template->get_name()));
      

        $trail->add(new Breadcrumb($this->get_template_suscribe_page_browser_url($this->template), Translation :: get('AddLocations')));
            
        $this->ab = $this->get_action_bar();
        $output = $this->get_page_subscribe_html();

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_page_subscribe_html()
    {
        $parameters = $this->get_parameters();
        
        $parameters[SurveyBuilder :: PARAM_BUILDER_ACTION] = SurveyBuilder :: ACTION_SUBSCRIBE_PAGE_BROWSER;
        $parameters[SurveyBuilder :: PARAM_TEMPLATE_ID ] = $this->template->get_id();
    	
        $table = new SurveyContextTemplateSubscribePageBrowserTable($this, $parameters, $this->get_subscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, Request :: get(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID));

        $survey_context_template_rel_pages = $this->retrieve_survey_context_template_rel_pages($condition);

        $conditions = array();
        while ($survey_context_template_rel_page = $survey_context_template_rel_pages->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(SurveyPage :: PROPERTY_ID, $survey_context_template_rel_page->get_page_id()));
        }

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_CITY, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_STREET, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0){
        	 return null;
        }
           

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_survey_context_template()
    {
        return $this->template;
    }

    function get_action_bar()
    {
        $template = $this->template;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_template_suscribe_page_browser_url($template));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_template_suscribe_page_browser_url($template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>