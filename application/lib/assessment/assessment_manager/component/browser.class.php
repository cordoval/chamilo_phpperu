<?php
/**
 * $Id: browser.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */


require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/assessment_publication_browser/assessment_publication_browser_table.class.php';

/**
 * assessment component which allows the user to browse his assessment_publications
 * @author Sven Vanpoucke
 * @author
 */
class AssessmentManagerBrowserComponent extends AssessmentManager
{
    private $action_bar;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseAssessmentPublications')));

        $this->action_bar = $this->get_action_bar();
        $menu = $this->get_menu();
        $trail->merge($menu->get_breadcrumbs());
        $this->display_header($trail);

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo '<div style="float: left; width: 17%; overflow: auto;">';
        echo $menu->render_as_tree();
        echo '</div>';
        echo '<div style="width: 80%; float: right;">';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $table = new AssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => AssessmentManager :: PARAM_APPLICATION, Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS), $this->get_condition());
        return $table->as_html();
    }

    function get_menu()
    {
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $menu = new AssessmentPublicationCategoryMenu($current_category);
        return $menu;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_assessment_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_assessment_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewResultsSummary'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_assessment_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportQTI'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_qti_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_condition()
    {
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;

        $query = $this->action_bar->get_query();

        $user = $this->get_user();
        $datamanager = AssessmentDataManager :: get_instance();

        if ($user->is_platform_admin())
        {
            $user_id = array();
            $groups = array();
        }
        else
        {
            $user_id = $user->get_id();
            $groups = $user->get_groups();
        }

        $conditions = array();

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition);
        }

        $access = array();
        $access[] = new EqualityCondition(AssessmentPublication :: PROPERTY_PUBLISHER, $user_id = $user->get_id());
        $access[] = new InCondition(AssessmentPublicationUser :: PROPERTY_USER, $user_id, $datamanager->get_alias(AssessmentPublicationUser :: get_table_name()));
        $access[] = new InCondition(AssessmentPublicationGroup :: PROPERTY_GROUP_ID, $groups, $datamanager->get_alias(AssessmentPublicationGroup :: get_table_name()));
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition(AssessmentPublicationUser :: PROPERTY_USER, null, $datamanager->get_alias(AssessmentPublicationUser :: get_table_name())), new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_GROUP_ID, null, $datamanager->get_alias(AssessmentPublicationGroup :: get_table_name()))));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(AssessmentPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(AssessmentPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(AssessmentPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(AssessmentPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new AndCondition(array(new EqualityCondition(AssessmentPublication :: PROPERTY_FROM_DATE, 0), new EqualityCondition(AssessmentPublication :: PROPERTY_TO_DATE, 0)));
            $dates[] = new EqualityCondition(AssessmentPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }

        $conditions[] = new EqualityCondition(AssessmentPublication :: PROPERTY_CATEGORY, $current_category);

        return new AndCondition($conditions);
    }
}
?>