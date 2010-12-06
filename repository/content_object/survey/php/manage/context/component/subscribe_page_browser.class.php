<?php
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
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
use common\libraries\InCondition;
use repository\content_object\survey_page\SurveyPage;


require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/context_template_rel_page_table/table.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/page_table/table.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/context_template_menu.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/survey_context_template_rel_page.class.php';

class SurveyContextManagerSubscribePageBrowserComponent extends SurveyContextManager
{

    const TAB_CONTEXT_TEMPLATE_REL_PAGE = 1;
    const TAB_ADD_PAGES = 2;

    private $ab;
    private $contex_template_id;
    private $survey_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $this->context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        $this->survey_id = Request :: get(self :: PARAM_SURVEY_ID);

        $this->ab = $this->get_action_bar();

        $output = $this->get_tabs_html();

        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';

        $menu = $this->get_menu_html();
        echo $menu;

        echo $output;
        $this->display_footer();
    }

    function get_tabs_html()
    {

        $html = array();
        $html[] = '<div>';
        $html[] = '<div style="float: right; width: 80%;">';

        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $parameters[self :: PARAM_CONTEXT_TEMPLATE_ID] = $this->context_template_id;

        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_CONTEXT_TEMPLATE_REL_PAGE;

        $table = new SurveyContextTemplateRelPageTable($this, $parameters, $this->get_context_template_rel_page_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_CONTEXT_TEMPLATE_REL_PAGE, Translation :: get('Pages'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));

        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_ADD_PAGES;
        $table = new SurveyPageTable($this, $parameters, $this->get_survey_page_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_ADD_PAGES, Translation :: get('AddPages'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));

        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");

    }

    function get_menu_html()
    {
        $template_menu = new SurveyContextTemplateMenu($this->contex_template_id, $this->survey_id);
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $template_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_context_template_rel_page_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->context_template_id);
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey_id);

        //        $query = $this->ab->get_query();
        //        if (isset($query) && $query != '')
        //        {
        //            $or_conditions = array();
        //            $or_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //            $or_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //            $or_condition = new OrCondition($or_conditions);
        //
        //            $and_conditions = array();
        //            $and_conditions[] = $condition;
        //            $and_conditions[] = $or_condition;
        //            $condition = new AndCondition($and_conditions);
        //        }


        return new AndCondition($conditions);
    }

    function get_survey_page_condition()
    {

        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->survey_id);

        $pages = $survey->get_pages();
        $page_ids = array();
        foreach ($pages as $page)
        {
            $page_ids[] = $page->get_id();
        }
		        
        $condition = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey_id);
        $context_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
        $context_template_rel_page_ids = array();
        while ($context_rel_page = $context_rel_pages->next_result())
        {
            $context_template_rel_page_ids[] = $context_rel_page->get_page_id();
        }
	 
        $diff = array_diff($page_ids, $context_template_rel_page_ids);
		if($diff){
			$condition = new InCondition(SurveyPage :: PROPERTY_ID, $diff);
		}else{
			$condition = new EqualityCondition(SurveyPage :: PROPERTY_ID, 0);
			
		}
        
        //
        //        $query = $this->ab->get_query();
        //        if (isset($query) && $query != '')
        //        {
        //            $or_conditions = array();
        //            $or_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //            $or_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
        //            $or_condition = new OrCondition($or_conditions);
        //
        //            $and_conditions = array();
        //            $and_conditions[] = $condition;
        //            $and_conditions[] = $or_condition;
        //            $condition = new AndCondition($and_conditions);
        //        }


        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(self :: PARAM_CONTEXT_TEMPLATE_ID => $this->contex_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => Request :: get(DynamicTabsRenderer :: PARAM_SELECTED_TAB))));

        return $action_bar;
    }
    
function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE)), Translation :: get('BrowseContextTemplates')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewContextTemplate')));

    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID, self :: PARAM_SURVEY_ID);
        
    }
}
?>