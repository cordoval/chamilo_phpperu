<?php
namespace repository\content_object\survey;

use common\libraries\Path;
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

class SurveyContextManagerContextTemplateViewerComponent extends SurveyContextManager
{
    
    const TAB_SURVEYS = 1;
    const TAB_ADD_TEMPLATE = 2;
    const TAB_TEMPLATES = 3;
    
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
        $parameters[self :: PARAM_CONTEXT_TEMPLATE_ID] = $this->context_template->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_SURVEYS;
        $table = new SurveyTable($this, $parameters, $this->get_survey_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SURVEYS, Translation :: get('Surveys'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_ADD_TEMPLATE;
        $table = new SurveyTable($this, $parameters, $this->get_survey_condition(false));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_ADD_TEMPLATE, Translation :: get('AddTemplates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_TEMPLATES;
        $table = new SurveyTemplateTable($this, $parameters, $this->get_template_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_TEMPLATES, Translation :: get('Templates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_template_condition()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyTemplate::PROPERTY_CONTEXT_TEMPLATE_ID, $this->context_template->get_id());
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyTemplate :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(SurveyTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyTemplate :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        $condition = new AndCondition($conditions);
        return $condition;
    }

    function get_survey_condition($with_template = true)
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id(), ContentObject :: get_table_name());
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Survey :: get_type_name(), ContentObject :: get_table_name());
        
        if ($with_template)
        {
            $conditions[] = new EqualityCondition(Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, $this->context_template->get_id(), Survey :: get_type_name());
        
        }
        else
        {
            $conditions[] = new EqualityCondition(Survey :: PROPERTY_CONTEXT_TEMPLATE_ID, 0, Survey :: get_type_name());
        
        }
        
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
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_template_creation_url($this->context_template), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE)), Translation :: get('BrowseContextTemplates')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewContextTemplate')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID);
    }

}
?>