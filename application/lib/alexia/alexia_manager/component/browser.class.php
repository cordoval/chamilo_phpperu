<?php
/**
 * $Id: browser.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager.component
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/alexia_publication_browser/alexia_publication_browser_table.class.php';

class AlexiaManagerBrowserComponent extends AlexiaManagerComponent
{
    private $introduction;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Alexia')));
        $trail->add_help('alexia general');

        $this->get_introduction();
        $this->action_bar = $this->get_action_bar();

        $this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->get_introduction_html();
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $this->get_publications_html();
        echo '</div>';
        $this->display_footer();
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);

        $table = new AlexiaPublicationBrowserTable($this, null, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $conditions = array();

        $user = $this->get_user();
        $datamanager = AlexiaDataManager :: get_instance();

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

        $subselect_conditions = array();
        $subselect_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'link');

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_conditions[] = new OrCondition($search_conditions);
        }

        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);

        $access = array();
        $access[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user_id = $user->get_id());
        $access[] = new InCondition(AlexiaPublicationUser :: PROPERTY_USER, $user_id, $datamanager->get_database()->get_alias(AlexiaPublicationUser :: get_table_name()));
        $access[] = new InCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, $groups, $datamanager->get_database()->get_alias(AlexiaPublicationGroup :: get_table_name()));
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition(AlexiaPublicationUser :: PROPERTY_USER, null, $datamanager->get_database()->get_alias(AlexiaPublicationUser :: get_table_name())), new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, null, $datamanager->get_database()->get_alias(AlexiaPublicationGroup :: get_table_name()))));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(AlexiaPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(AlexiaPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(AlexiaPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }

        return new AndCondition($conditions);
    }

    function get_introduction()
    {
        $subselect_condition = new EqualityCondition('type', 'introduction');
        $condition = new SubselectCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);

        $publications = AlexiaDataManager :: get_instance()->retrieve_alexia_publications($condition);
        if (! $publications->is_empty())
        {
            $this->introduction = $publications->next_result();
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_CREATE_PUBLICATION))));
        if (! isset($this->introduction))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddIntroduction'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_PUBLISH_INTRODUCTION))));
        }
        $action_bar->set_search_url($this->get_url());
        //		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
        return $action_bar;
    }

    function get_introduction_html()
    {
        $introduction = $this->introduction;
        $html = array();

        if (isset($introduction))
        {

            $tb_data[] = array('href' => $this->get_introduction_editing_url($introduction), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $tb_data[] = array('href' => $this->get_publication_deleting_url($introduction), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON);

            $object = $introduction->get_publication_object();

            $html[] = '<div class="introduction" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/introduction.png);">';
            $html[] = '<div class="title">';
            $html[] = $object->get_title();
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '<div class="description">';
            $html[] = $object->get_description();
            $html[] = '</div>';
            $html[] = Utilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        return implode("\n", $html);
    }
}
?>