<?php
namespace repository;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\ResourceManager;
use common\libraries\Translation;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\NotCondition;
use common\libraries\InCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\PatternMatchCondition;

use repository\content_object\learning_path_item\LearningPathItem;

use admin\AdminDataManager;
use admin\Registration;

/**
 * $Id: browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerBrowserComponent extends RepositoryManager
{
    private $form;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $this->form = new RepositoryFilterForm($this, $this->get_url(array('category' => $this->get_parent_id())));
        $output = $this->get_content_objects_html();

        $query = $this->get_action_bar()->get_query();
        if (isset($query) && $query != '')
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES)));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchResultsFor', null, Utilities :: COMMON_LIBRARIES) . ' ' . $query));
        }

        $session_filter = Session :: retrieve('filter');

        if ($session_filter != null && ! $session_filter == 0)
        {
            if (is_numeric($session_filter))
            {
                $condition = new EqualityCondition(UserView :: PROPERTY_ID, $session_filter);
                $user_view = RepositoryDataManager :: get_instance()->retrieve_user_views($condition)->next_result();
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES) . ': ' . $user_view->get_name()));
            }
            else
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES) . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
        }

        $this->display_header($trail, false, true);
        echo $this->get_action_bar()->as_html();
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
        $renderer = ContentObjectRenderer :: factory($this->get_renderer(), $this);
        return $renderer->as_html();
    }

    function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            $this->action_bar->set_search_url($this->get_url(array('category' => $this->get_parent_id())));

            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $renderers = $this->get_available_renderers();

            if (count($renderers) > 1)
            {
                foreach ($renderers as $renderer)
                {
                    $this->action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($renderer) . 'View', null, Utilities :: COMMON_LIBRARIES), Theme :: get_image_path() . 'view_' . $renderer . '.png', $this->get_url(array(RepositoryManager :: PARAM_RENDERER => $renderer)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }

            $this->action_bar->add_tool_action(new ToolbarItem(Translation :: get('ExportEntireRepository'), Theme :: get_common_image_path() . 'action_backup.png', $this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => 'all')), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $this->action_bar;
    }

    public function get_condition()
    {
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->get_parent_id());
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());

        $types = RepositoryDataManager :: get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type));
        }

        $query = $this->get_action_bar()->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');

            $conditions[] = new OrCondition($or_conditions);
        }

        $cond = $this->form->get_filter_conditions();
        if ($cond)
        {
            $conditions[] = $cond;
        }

        $conditions[] = new InCondition(ContentObject :: PROPERTY_TYPE, RepositoryDataManager :: get_registered_types());
        $condition = new AndCondition($conditions);
        //dump($condition);
        return $condition;
    }

    private function get_parent_id()
    {
        return Request :: get(RepositoryManager :: PARAM_CATEGORY_ID) ? Request :: get(RepositoryManager :: PARAM_CATEGORY_ID) : 0;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_browser');
    }

    function is_allowed_to_create($type)
    {
        return true;
    }

    function get_allowed_content_object_types()
    {
        $types = $this->get_content_object_types(true, false);
        foreach ($types as $index => $type)
        {
            $registration = AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT);
            if (! $registration || ! $registration->is_active())
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

}
?>