<?php

require_once dirname(__FILE__) . '/context_template_subscribe_page_browser/subscribe_page_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey_page/survey_page.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';

class SurveyContextManagerContextTemplateSubscribePageBrowserComponent extends SurveyContextManager
{
    	
	private $context_template;
    private $survey_id;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_template_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID);
        $this->context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        $this->survey_id = Request :: get(SurveyContextManager :: PARAM_SURVEY_ID);
        
        $this->ab = $this->get_action_bar();
        $output = $this->get_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_html()
    {
        $parameters = $this->get_parameters();
        $parameters[SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID] = $this->context_template->get_id();
        $parameters[SurveyContextManager :: PARAM_SURVEY_ID] = $this->survey_id;
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        $table = new SurveyContextTemplateSubscribePageBrowserTable($this, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        
        $survey_id = $this->survey_id;
        $root_conditions = array();
        $root_conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID));
        $root_conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id);
        $condition = new AndCondition($root_conditions);
        $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
        
        $template_pages = array();
        while ($template_rel_page = $template_rel_pages->next_result())
        {
            $template_pages[] = $template_rel_page->get_page_id();
        }
        
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        
        $pages = $survey->get_pages();
        
        $survey_pages = array();
        foreach ($pages as $page)
        {
            $survey_pages[] = $page->get_id();
        }
        
        $not_template_pages = array_diff($survey_pages, $template_pages);
        
        $conditions = array();
        
        if (! count($not_template_pages))
        {
            $not_template_pages[] = 0;
        }
        $conditions[] = new InCondition(SurveyPage :: PROPERTY_ID, $not_template_pages, SurveyPage :: get_table_name());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(SurveyPage :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }
        
        if (count($conditions) == 0)
        {
            return null;
        }
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_survey_context_template()
    {
        return $this->context_template;
    }

    function get_action_bar()
    {
        $template = $this->context_template;
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        //        $action_bar->set_search_url($this->get_template_suscribe_page_browser_url($template));
        

        //        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_template_suscribe_page_browser_url($template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE)), Translation :: get('BrowseContextTemplates')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewContextTemplate')));
//        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_USERS)), Translation :: get('ViewSurveyContext')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID, self :: PARAM_SURVEY_ID);
    }

}
?>