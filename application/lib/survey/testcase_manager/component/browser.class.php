<?php

require_once dirname(__FILE__) . '/publication_browser/publication_browser_table.class.php';

class TestcaseManagerBrowserComponent extends TestcaseManager
{
    private $action_bar;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
        
        $this->action_bar = $this->get_action_bar();
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo '<div >';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters();
       	$table = new TestcaseSurveyPublicationBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateTestCase'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowseSurveyPublications'), Theme :: get_common_image_path() . 'action_category.png', $this->get_parent()->get_browse_survey_publications_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        
        $user = $this->get_user();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_TEST, true);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        }
        
        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $user->get_id());
        
        return new AndCondition($conditions);
    }
}
?>