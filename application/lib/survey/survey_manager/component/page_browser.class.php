<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManagerPageBrowserComponent extends SurveyManagerComponent
{
    private $action_bar;
    private $survey_ids;

    function run()
    {
        
    	$ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
              
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
        } else{
        	$ids = array();
        }  
    	
    	$this->survey_ids = $ids;
    	
    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        
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
        $table = new SurveyPageBrowserTable($this, array(Application :: PARAM_APPLICATION => SurveyManager :: APPLICATION_NAME, Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        //        if (SurveyRights :: is_allowed(SurveyRights :: ADD_RIGHT, 'publication_browser', 'survey_component'))
        //        {
        //            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        //        
        //        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        
        //        if (SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'category_manager', 'survey_component'))
        //        {
        //            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_survey_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        //        
        //        if (SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'testcase_browser', 'survey_component'))
        //        {
        //            $action_bar->add_common_action(new ToolbarItem(Translation :: get('TestSurveys'), Theme :: get_common_image_path() . 'action_category.png', $this->get_testcase_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        

        //        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewResultsSummary'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_survey_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportSurvey'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_survey_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        
        $condition = null;
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new OrCondition($search_conditions);
        }
        return $condition;
    
    }

    function get_survey_ids()
    {
        return $this->survey_ids;
    }

}

?>