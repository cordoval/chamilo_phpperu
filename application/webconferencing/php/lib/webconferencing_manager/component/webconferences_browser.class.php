<?php
/**
 * $Id: webconferences_browser.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component
 */

require_once dirname(__FILE__) . '/../webconferencing_manager.class.php';
require_once dirname(__FILE__) . '/webconference_browser/webconference_browser_table.class.php';

/**
 * webconferencing component which allows the user to browse his webconferences
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferencesBrowserComponent extends WebconferencingManager
{

    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Webconferencing')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWebconferences')));
        $trail->add_help('webconferencing general');
        $this->action_bar = $this->get_action_bar();

        $toolbar = $this->get_action_bar();

        $this->display_header($trail);
        echo $toolbar->as_html();
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $table = new WebconferenceBrowserTable($this, array(Application :: PARAM_APPLICATION => 'webconferencing', Application :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES), $this->get_condition());
        return $table->as_html();
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateWebconference'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_webconference_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();

        $user = $this->get_user();
        $datamanager = WebconferencingDataManager :: get_instance();

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
            $search_conditions[] = new PatternMatchCondition(Webconference :: PROPERTY_CONFNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(Webconference :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }

        $access = array();
        $access[] = new EqualityCondition(Webconference :: PROPERTY_USER_ID, $user_id = $user->get_id());
        $access[] = new InCondition(WebconferenceUser :: PROPERTY_USER, $user_id, WebconferenceUser :: get_table_name());
        $access[] = new InCondition(WebconferenceGroup :: PROPERTY_GROUP_ID, $groups, WebconferenceGroup :: get_table_name());
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition(WebconferenceUser :: PROPERTY_USER, null, WebconferenceUser :: get_table_name()), new EqualityCondition(WebconferenceGroup :: PROPERTY_GROUP_ID, null, WebconferenceGroup :: get_table_name())));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(Webconference :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(Webconference :: PROPERTY_USER_ID, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(Webconference :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(Webconference :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new AndCondition(array(new EqualityCondition(Webconference :: PROPERTY_FROM_DATE, 0), new EqualityCondition(Webconference :: PROPERTY_TO_DATE, 0)));
            $dates[] = new EqualityCondition(Webconference :: PROPERTY_USER_ID, $user->get_id());
            $conditions[] = new OrCondition($dates);

        }

        return new AndCondition($conditions);
    }

}
?>