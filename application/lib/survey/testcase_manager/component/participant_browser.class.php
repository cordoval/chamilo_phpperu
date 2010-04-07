<?php

//require_once dirname(__FILE__) . '/../testcase_manager.class.php';
//require_once dirname(__FILE__) . '/../testcase_manager_component.class.php';
require_once dirname(__FILE__) . '/participant_browser/participant_browser_table.class.php';

class TestcaseManagerParticipantBrowserComponent extends TestcaseManagerComponent
{
    private $action_bar;
    private $pid;

    function run()
    {
        
        $this->pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        if (! isset($this->pid))
        {
            $this->pid = Request :: post(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        }
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_survey_publication_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTestCaseSurveyParticipants')));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters();
        $parameters[SurveyManager :: PARAM_SURVEY_PUBLICATION] = $this->pid;
       	$parameters[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =  $this->action_bar->get_query();
        $table = new TestcaseSurveyParticipantBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $parameters = $this->get_parameters();
        $parameters[SurveyManager :: PARAM_SURVEY_PUBLICATION] = $this->pid;
        
        $action_bar->set_search_url($this->get_url($parameters));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
       
        return $action_bar;
    }

    function get_condition()
    {
        
        $query = $this->action_bar->get_query();
        if(! isset($query)){
        	$query = Request :: get(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY);
        }
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
        
        if (isset($query) && $query != '')
        {
            $user_conditions = array();
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*'.$query.'*');
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME,  '*'.$query.'*');
            $user_condition = new OrCondition($user_conditions);
            $users = UserDataManager :: get_instance()->retrieve_users($user_condition);
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME, '*'.$query.'*');
            $search_conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $user_ids);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }
}
?>