<?php
namespace repository\content_object\survey;

use common\libraries\InCondition;

use user\UserDataManager;

use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\ActionBarSearchForm;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\AndCondition;
use repository\ContentObject;
use user\User;

class SurveyContextManagerTemplateViewerComponent extends SurveyContextManager
{
    
    const TAB_TEMPLATE_USERS = 1;
    const TAB_USERS = 2;
    
    private $ab;
    private $template;
    private $context_template;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $template_id = Request :: get(SurveyContextManager :: PARAM_TEMPLATE_ID);
        $this->template = SurveyContextDataManager :: get_instance()->retrieve_survey_template($template_id);
        $this->context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->template->get_context_template_id());
        
        $this->ab = $this->get_action_bar();
        
        $output = $this->get_tabs();
        
        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[self :: PARAM_TEMPLATE_ID] = $this->template->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_TEMPLATE_USERS;
        $table = new SurveyTemplateUserTable($this, $parameters, $this->get_template_user_condition(), $this->context_template);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_TEMPLATE_USERS, Translation :: get('Templates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_USERS;
        $table = new SurveyUserTable($this, $parameters, $this->get_user_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('Users',null,Utilities::COMMON_LIBRARIES), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

 function get_user_condition()
    {
    	
//    	$template_condition = new EqualityCondition(SurveyTemplate::PROPERTY_CONTEXT_TEMPLATE_ID, $this->context_template->get_id());
//    	$templates = SurveyContextDataManager::get_instance()->retrieve_survey_templates($template_condition);
//    	$template_ids = array();
//    	while ($template = $templates->next_result()){
//    		$template_ids[] = $template->get_id();
//    	}
//    	$template_user_condition = new InCondition(SurveyTemplateUser::PROPERTY_TEMPLATE_ID, $template_ids, SurveyTemplateUser :: get_table_name());
    	
    	$template_users = SurveyContextDataManager::get_instance()->retrieve_survey_template_users($this->context_template->get_type());
    	
    	$user_ids = array();
    	while ($template_user = $template_users->next_result()){
    		$user_ids[] = $template_user->get_user_id();
    	}
    	
    	if(count($user_ids) == 0){
    		$user_ids[] = 0;
    	}
    	
    	$conditions = array();
        $conditions[] = new InCondition(User::PROPERTY_ID, $user_ids);
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', User :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', User :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', User :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        $condition = new AndCondition($conditions);
        return $condition;
    }
    
    function get_template_user_condition()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyTemplateUser :: PROPERTY_TEMPLATE_ID, $this->template->get_id(), SurveyTemplateUser :: get_table_name());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $user_condition = new OrCondition($search_conditions);
            
            $users = UserDataManager :: get_instance()->retrieve_users($user_condition);
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
            if (! count($user_ids))
            {
                $user_ids[] = 0;
            }
            
            $conditions[] = new InCondition(SurveyTemplateUser :: PROPERTY_USER_ID, $user_ids, SurveyTemplateUser :: get_table_name());
        
        }
        $condition = new AndCondition($conditions);
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_template_user_creation_url($this->template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_import.png', $this->get_template_user_import_url($this->template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE)), Translation :: get('BrowseContextTemplates')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, 
                self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewObject', array("OBJECT" => Translation::get('ContextTemplate')), Utilities::COMMON_LIBRARIES)));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_TEMPLATE, 
                self :: PARAM_TEMPLATE_ID => Request :: get(self :: PARAM_TEMPLATE_ID))), Translation :: get('ViewTemplate')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID, self :: PARAM_TEMPLATE_ID);
    }

}
?>