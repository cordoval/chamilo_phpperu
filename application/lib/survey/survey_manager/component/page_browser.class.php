<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/page_browser/table.class.php';

class SurveyManagerPageBrowserComponent extends SurveyManager
{
    private $action_bar;
    private $survey_ids;

    function run()
    {
        
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY);
        
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
        
        $this->survey_ids = $ids;
        
        $trail = BreadcrumbTrail :: get_instance();
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
        $table = new SurveyPageBrowserTable($this, array(Application :: PARAM_APPLICATION => SurveyManager :: APPLICATION_NAME, Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PAGES), $this->get_condition());
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

    function get_survey_ids()
    {
        return $this->survey_ids;
    }

}

?>