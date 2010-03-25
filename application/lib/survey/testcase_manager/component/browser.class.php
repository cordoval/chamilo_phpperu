<?php

//require_once dirname(__FILE__) . '/../testcase_manager.class.php';
//require_once dirname(__FILE__) . '/../testcase_manager_component.class.php';
require_once dirname(__FILE__) . '/publication_browser/publication_browser_table.class.php';

/**
 * survey component which allows the user to browse his survey_publications
 * @author Sven Vanpoucke
 * @author
 */
class TestcaseManagerBrowserComponent extends TestcaseManagerComponent
{
    private $action_bar;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTestcaseSurveyPublications')));

        $this->action_bar = $this->get_action_bar();
//        $menu = $this->get_menu();
//        $trail->merge($menu->get_breadcrumbs());
        $this->display_header($trail);

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
//        echo '<div style="float: left; width: 17%; overflow: auto;">';
//        echo $menu->render_as_tree();
//        echo '</div>';
        echo '<div >';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $table = new TestcaseSurveyPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'survey', Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), $this->get_condition());
        return $table->as_html();
    }

//    function get_menu()
//    {
//        $current_category = Request :: get('category');
//        $current_category = $current_category ? $current_category : 0;
//        $menu = new SurveyPublicationCategoryMenu($current_category);
//        return $menu;
//    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateTestCase'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_survey_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowseSurveys'), Theme :: get_common_image_path() . 'action_category.png', $this->get_parent()->get_parent()->get_browse_survey_publications_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


//        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewTestcaseResultsSummary'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_survey_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportSurvey'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_survey_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_condition()
    {
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;

        $query = $this->action_bar->get_query();

        $user = $this->get_user();
        $datamanager = SurveyDataManager :: get_instance();

        if ($user->is_platform_admin())
        {
            $user_id = array();
            $groups = array();
        }
        else
        {
            $user_id = $user->get_id();
            $groups = $user->get_groups(true);
        }

        $conditions = array();
		$conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_TEST, true );

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        }

        $access = array();
        $access[] = new InCondition(SurveyPublicationUser :: PROPERTY_USER, $user_id, $datamanager->get_database()->get_alias(SurveyPublicationUser :: get_table_name()));
        $access[] = new InCondition(SurveyPublicationGroup :: PROPERTY_GROUP_ID, $groups, $datamanager->get_database()->get_alias(SurveyPublicationGroup :: get_table_name()));
        $access[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $user->get_id() ); //= );


        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array( new InCondition(SurveyPublicationGroup :: PROPERTY_GROUP_ID, $groups, $datamanager->get_database()->get_alias(SurveyPublicationGroup :: get_table_name())),new EqualityCondition(SurveyPublicationUser :: PROPERTY_USER, $user_id, $datamanager->get_database()->get_alias(SurveyPublicationUser :: get_table_name()))));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(SurveyPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(SurveyPublication :: PROPERTY_TEST, false);

            $visibility[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(SurveyPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(SurveyPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new AndCondition(array(new EqualityCondition(SurveyPublication :: PROPERTY_FROM_DATE, 0), new EqualityCondition(SurveyPublication :: PROPERTY_TO_DATE, 0)));
            $dates[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }

        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_CATEGORY, $current_category);

        return new AndCondition($conditions);
    }
}
?>