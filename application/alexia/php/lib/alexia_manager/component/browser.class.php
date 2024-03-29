<?php

namespace application\alexia;

use common\libraries\WebApplication;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use repository\ContentObject;
use repository\content_object\link\Link;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\AndCondition;
use common\libraries\InequalityCondition;
use common\libraries\InCondition;
use repository\content_object\introduction\Introduction;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\Toolbar;
use common\libraries\SubselectCondition;
use repository\RepositoryDataManager;
use common\libraries\Utilities;
/**
 * $Id: browser.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('alexia') . 'alexia_manager/component/alexia_publication_browser/alexia_publication_browser_table.class.php';

class AlexiaManagerBrowserComponent extends AlexiaManager
{
    private $introduction;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
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
        $subselect_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Link :: get_type_name());

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $subselect_conditions[] = new OrCondition($search_conditions);
        }

        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());

        $access = array();
        $access[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user_id = $user->get_id());
        $access[] = new InCondition(AlexiaPublicationUser :: PROPERTY_USER, $user_id, AlexiaPublicationUser :: get_table_name());
        $access[] = new InCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, $groups, AlexiaPublicationGroup :: get_table_name());
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition(AlexiaPublicationUser :: PROPERTY_USER, null, AlexiaPublicationUser :: get_table_name()), new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, null, AlexiaPublicationGroup :: get_table_name())));
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
        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $condition = new SubselectCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());

        $publications = AlexiaDataManager :: get_instance()->retrieve_alexia_publications($condition);
        if (! $publications->is_empty())
        {
            $this->introduction = $publications->next_result();
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_CREATE_PUBLICATION))));
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
        	$toolbar = new Toolbar();
        	$toolbar->add_item(new ToolbarItem(Translation :: get('Edit',null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->get_introduction_editing_url($introduction), ToolbarItem :: DISPLAY_ICON));
			$toolbar->add_item(new ToolbarItem(Translation :: get('Delete',null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->get_publication_deleting_url($introduction), ToolbarItem :: DISPLAY_ICON, true));

            $object = $introduction->get_publication_object();

            $html[] = '<div class="introduction" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/introduction.png);">';
            $html[] = '<div class="title">';
            $html[] = $object->get_title();
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '<div class="description">';
            $html[] = $object->get_description();
            $html[] = '</div>';
            $html[] = $toolbar->as_html() . '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        return implode("\n", $html);
    }
}
?>