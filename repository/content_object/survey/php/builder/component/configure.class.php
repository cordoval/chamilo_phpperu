<?php
namespace repository\content_object\survey;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\EqualityCondition;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\ActionBarSearchForm;
use repository\ComplexContentObjectItem;

class SurveyBuilderConfigureComponent extends SurveyBuilder
{
    
    const PAGE_CONFIGS_TAB = 1;
	const PAGE_QUESTIONS_TAB = 2;
    
    const VISIBLE_QUESTION_ID = 'visible_question_id';
    const INVISIBLE_QUESTION_ID = 'invisible_question_id';
    const ANSWERMATCH = 'answer_match';
    
    private $page_id;
    private $action_bar;

    function run()
    {
        
        $this->page_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID);
        
        
        $this->action_bar = $this->get_action_bar();
        $this->display_header();
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $this->get_tables();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    private function get_tables()
    {
        $parameters = $this->get_parameters();
        $table = new SurveyPageQuestionBrowserTable($this, $parameters, $this->get_condition());
        
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_CONFIGS_TAB;
        $table = new SurveyPageConfigTable($this, $parameters, $this->get_page_config_condition());
        $tabs->add_tab(new DynamicContentTab(self :: PAGE_CONFIGS_TAB, Translation :: get('PageConfigs'), Theme :: get_image_path() . 'logo/16.png', $table->toHTML()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_QUESTIONS_TAB;
        $table = new SurveyPageQuestionBrowserTable($this, $parameters, $this->get_condition());
        $tabs->add_tab(new DynamicContentTab(self :: PAGE_QUESTIONS_TAB, Translation :: get('PageQuestions'), Theme :: get_image_path() . 'logo/16.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        
        $page_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID);
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $page_id, ComplexContentObjectItem :: get_table_name());
        return $condition;
    }
	
	function get_page_config_condition()
    {
        
        $page_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID);
        return $page_id;
    }
    
    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID);
    }

}

?>