<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\ResourceManager;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\InCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\NotCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\ConditionProperty;

/**
 * $Id: shared_content_objects_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerSharedContentObjectsBrowserComponent extends RepositoryManager
{
    const VIEW_OTHERS_OBJECTS = 0;
    const VIEW_OWN_OBJECTS = 1;

    private $form;
    private $view;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->view = Request :: get(self :: PARAM_SHOW_OBJECTS_SHARED_BY_ME);
        if (is_null($this->view))
            $this->view = self :: VIEW_OTHERS_OBJECTS;

        $trail = BreadcrumbTrail :: get_instance();

        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());
        $output = $this->get_content_objects_html();

        //$query = $this->action_bar->get_query();
        //if(isset($query) && $query != '')
        //{
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Search')));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchResultsFor').': '.$query));
        //}


        $session_filter = Session :: retrieve('filter');

        if ($session_filter != null && ! $session_filter == 0)
        {
            if (is_numeric($session_filter))
            {
                $condition = new EqualityCondition(UserView :: PROPERTY_ID, $session_filter);
                $user_view = RepositoryDataManager :: get_instance()->retrieve_user_views($condition)->next_result();
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . $user_view->get_name()));
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
            }
        }

        $this->display_header($trail, false, true);

        echo $this->action_bar->as_html();
        echo '<br />' . $this->form->display() . '<br />';
        echo $output;
        echo ResourceManager :: get_instance()->get_resource_html(BasicApplication :: get_application_web_resources_javascript_path(RepositoryManager :: APPLICATION_NAME) . 'repository.js');

        $this->display_footer();
    }

    /**
     * Gets the  table which shows the learning objects in the currently active
     * category
     */
    private function get_content_objects_html()
    {
        $condition = null;
        switch ($this->view)
        {
            case self :: VIEW_OWN_OBJECTS :
                $condition = $this->get_view_own_objects_condition();
                break;
            case self :: VIEW_OTHERS_OBJECTS :
                $condition = $this->get_view_other_objects_condition();
                break;
            default :
                $condition = new EqualityCondition(ContentObject :: PROPERTY_ID, - 1);
        }

        $search_condition = $this->action_bar->get_conditions(array(new ConditionProperty(ContentObject :: PROPERTY_TITLE), new ConditionProperty(ContentObject :: PROPERTY_DESCRIPTION)));
        if ($search_condition)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = $search_condition;
            $condition = new AndCondition($conditions);
        }

        $parameters = $this->get_parameters(true);
        $types = Request :: get(ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE);

        if (is_array($types) && count($types))
        {
            $parameters[ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new RepositorySharedContentObjectsBrowserTable($this, $parameters, $condition);

        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_view_own_objects_condition()
    {
        $conditions = $subconditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());

        $subconditions[] = new NotCondition(new EqualityCondition(ContentObjectUserShare :: PROPERTY_USER_ID, null, ContentObjectUserShare :: get_table_name()));
        $subconditions[] = new NotCondition(new EqualityCondition(ContentObjectGroupShare :: PROPERTY_GROUP_ID, null, ContentObjectGroupShare :: get_table_name()));

        $conditions[] = new OrCondition($subconditions);
        return new AndCondition($conditions);
    }

    function get_view_other_objects_condition()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(ContentObjectUserShare :: PROPERTY_USER_ID, $this->get_user_id(), ContentObjectUserShare :: get_table_name());

        $group_ids = array();
        $groups = $this->get_user()->get_groups();
        if ($groups)
        {
            while ($group = $groups->next_result())
            {
                $group_ids[] = $group->get_id();
            }

            $conditions[] = new InCondition(ContentObjectGroupShare :: PROPERTY_GROUP_ID, $group_ids, ContentObjectGroupShare :: get_table_name());
        }

        return new OrCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_shared_content_object_browser');
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SHOW_OBJECTS_SHARED_BY_ME);
    }

    function show_my_objects()
    {
        return Request :: get(self :: PARAM_SHOW_OBJECTS_SHARED_BY_ME) == 1;
    }
}
?>