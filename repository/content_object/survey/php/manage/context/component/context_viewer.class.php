<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/context_rel_user_table/table.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/context_rel_group_table/table.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_rel_user.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_rel_group.class.php';

class SurveyContextManagerContextViewerComponent extends SurveyContextManager
{
    
    const TAB_USERS = 1;
    const TAB_GROUPS = 2;
    
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
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_GROUPS;
        $table = new SurveyContextRelGroupTable($this, $parameters, $this->get_group_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_GROUPS, Translation :: get('Groups'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_user_condition()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $this->context->get_id());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_group_condition()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $this->context->get_id());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $group_alias = GroupDataManager :: get_instance()->get_alias(Group :: get_table_name());
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*', $group_alias, true);
            $search_conditions[] = new PatternMatchCondition(Group :: PROPERTY_DESCRIPTION, '*' . $query . '*', $group_alias, true);
            $conditions = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $parameters = $this->get_parameters();
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = Request :: get(DynamicTabsRenderer :: PARAM_SELECTED_TAB);
        $action_bar->set_search_url($this->get_url($parameters));
        
        //     if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_USER_RIGHT, $this->period->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
        //        {
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddContextUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_context_subscribe_user_url($this->context), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        //        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_GROUP_RIGHT, $this->period->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
        //        {
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddContextGroups'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_context_subscribe_group_url($this->context), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        

        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseContextRegistrations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID))), Translation :: get('ViewContextRegistration')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID, self :: PARAM_CONTEXT_ID);
    }

}
?>