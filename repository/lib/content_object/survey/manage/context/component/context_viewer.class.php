<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/context_rel_user_table/table.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_rel_user.class.php';


class SurveyContextManagerContextViewerComponent extends SurveyContextManager
{
    	
	const TAB_USERS = 'users';
	
	private $ab;
    private $context;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_ID);
        $this->context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);

        $this->ab = $this->get_action_bar();
        
        $output = $this->get_tabs_html();
        
        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs_html()
    {
         $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[self :: PARAM_CONTEXT_ID] = $this->context->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_USERS;
        $table = new SurveyContextRelUserBrowserTable($this, $parameters, $this->get_user_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('Users'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
              
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_user_condition()
    {
        
    	$conditions = array();
    	$conditions[] = new EqualityCondition(SurveyContextRelUser::PROPERTY_CONTEXT_ID, $this->context->get_id());
    	
    	$query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(SurveyContext :: PROPERTY_NAME, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            //            $conditions[] = new PatternMatchCondition(SurveyContextRegistration :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            $condition = new OrCondition($conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_creation_url($this->context_registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseContextRegistrations')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_ID);
    }

}
?>