<?php namespace repository\content_object\survey;

use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolBarItem;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ActionBarSearchForm;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;


require_once dirname(__FILE__) . '/context_table/table.class.php';

class SurveyContextManagerRegistrationViewerComponent extends SurveyContextManager
{
    
	const TAB_CONTEXTS = 'contexts';
	const TAB_USERS = 'users';
	
	private $ab;
    private $context_registration;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
//        $this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);
        $this->context_registration = SurveyContextDataManager :: get_instance()->retrieve_survey_context_registration($context_registration_id);

        $this->ab = $this->get_action_bar();
        
        $output = $this->get_tabs_html();
        
        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs_html()
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
        
    	 $conditions = array();
    	 $context_alias = SurveyContextDataManager :: get_instance()->get_alias(SurveyContext :: get_table_name());
    	 $conditions[] = new EqualityCondition(SurveyContext::PROPERTY_CONTEXT_REGISTRATION_ID, $this->context_registration->get_id(), SurveyContext :: get_table_name());
    	
    	$query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyContext :: PROPERTY_NAME, '*' . $query . '*', SurveyContext :: get_table_name());
            //            $conditions[] = new PatternMatchCondition(SurveyContextRegistration :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextRegistration :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_creation_url($this->context_registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Import', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_import.png', $this->get_context_import_url($this->context_registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseContextRegistrations')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID);
    }

}
?>