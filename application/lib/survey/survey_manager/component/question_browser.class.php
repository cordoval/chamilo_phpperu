<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/question_browser/table.class.php';

class SurveyManagerQuestionBrowserComponent extends SurveyManager
{
    private $action_bar;
    private $page_ids;

    function run()
    {
        
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_PAGE_ID);
              
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
        }
        else
        {
            $ids = array();
        }
        
        $this->page_ids = $ids;

        $this->action_bar = $this->get_action_bar();
        $this->display_header();
        
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
        $table = new SurveyQuestionBrowserTable($this, array(Application :: PARAM_APPLICATION => SurveyManager :: APPLICATION_NAME, Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_PAGE_QUESTIONS), $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        
        $condition = null;
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', ContentObject :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', ContentObject :: get_table_name());
            $condition = new OrCondition($search_conditions);
        }
        return $condition;
    
    }

    function get_page_ids()
    {
        return $this->page_ids;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PAGES, self ::PARAM_SURVEY_ID => Request :: get(self :: PARAM_SURVEY_ID))), Translation :: get('BrowseSurveyPages')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_ID, self :: PARAM_SURVEY_PAGE_ID);
    }

}

?>