<?php

/**
 * $Id: system_announcement_browser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Admin component to manage system announcements
 */
class AdminManagerSystemAnnouncementBrowserComponent extends AdminManager
{

    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('administration system announcements');

        if (!AdminRights::is_allowed(AdminRights::RIGHT_VIEW, AdminRights::LOCATION_SYSTEM_ANNOUNCEMENTS))
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $user = $this->get_user();

        if (!$user->is_platform_admin())
        {
            $this->not_allowed();
        }
        $this->action_bar = $this->get_action_bar();

        $publications_table = $this->get_publications_html();
        $toolbar = $this->get_action_bar();

        $this->display_header();

        echo $toolbar->as_html();
        echo '<div id="action_bar_browser">';
        echo $publications_table;
        echo '</div>';
        $this->display_footer();
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

        $table = new SystemAnnouncementPublicationBrowserTable($this, null, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (!Request :: get('pid'))
        {
            $action_bar->set_search_url($this->get_url());
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_system_announcement_publication_creating_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EditRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_rights_editor_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_condition()
    {
        $condition = null;
        $user = $this->get_user();

        if (!$user->is_platform_admin())
        {
            $conditions = array();

            $conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_HIDDEN, false);

            $time_conditions = array();

            $forever_conditions = array();
            $forever_conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_FROM_DATE, 0);
            $forever_conditions[] = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_TO_DATE, 0);
            $time_conditions[] = new AndCondition($forever_conditions);

            $limited_conditions = array();
            $limited_conditions[] = new InequalityCondition(SystemAnnouncementPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
            $limited_conditions[] = new InequalityCondition(SystemAnnouncementPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
            $time_conditions[] = new AndCondition($limited_conditions);

            $conditions[] = new OrCondition($time_conditions);

            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

}

?>