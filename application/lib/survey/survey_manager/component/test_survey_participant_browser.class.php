<?php
/**
 * $Id: browser.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/test_survey_participant_browser/test_survey_participant_browser_table.class.php';

/**
 * survey component which allows the user to browse his survey_publications
 * @author Sven Vanpoucke
 * @author
 */
class SurveyManagerTestSurveyParticipantBrowserComponent extends SurveyManagerComponent
{
    private $action_bar;
    private $pid;

    function run()
    {

        $this->pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url( array(SurveyManager:: PARAM_ACTION => SurveyManager::ACTION_BROWSE_TEST_SURVEY_PUBLICATION)), Translation :: get('BrowseTestSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTestSurveyParticipants')));

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
        $table = new TestSurveyParticipantBrowserTable($this, array(Application :: PARAM_APPLICATION => 'survey', Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_TEST_SURVEY_PARTICIPANTS), $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_survey_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowseSurveys'), Theme :: get_common_image_path() . 'action_category.png', $this->get_browse_survey_publications_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewTestResultsSummary'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_survey_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportSurvey'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_survey_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }

    function get_condition()
    {

        $query = $this->action_bar->get_query();

        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        }

        return new AndCondition($conditions);
    }
}
?>