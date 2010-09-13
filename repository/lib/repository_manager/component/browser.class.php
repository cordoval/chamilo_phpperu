<?php
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
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Search')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchResultsFor') . ' ' . $query));
        }

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
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
        }

        $this->display_header($trail, false, true);

        echo $this->get_action_bar()->as_html();
        echo '<br />' . $this->form->display() . '<br />';
        echo $output;
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');

        $this->display_footer();
    }

    /**
     * Gets the  table which shows the learning objects in the currently active
     * category
     */
    private function get_content_objects_html()
    {
//        $condition = $this->get_condition();
//        $parameters = $this->get_parameters(true);
//        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
//        if (is_array($types) && count($types))
//        {
//            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
//        }
//
//        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->get_action_bar()->get_query();

        $renderer = ContentObjectRenderer :: factory($this->get_renderer(), $this);
        return $renderer->as_html();

    //        $table = new RepositoryBrowserGalleryTable($this, $parameters, $condition);
    //        return $table->as_html();
    }

    function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            $this->action_bar->set_search_url($this->get_url(array('category' => $this->get_parent_id())));

            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(
                    Application :: PARAM_ACTION => RepositoryManager :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $renderers = $this->get_available_renderers();

            if (count($renderers) > 1)
            {
                foreach ($renderers as $renderer)
                {
                    $this->action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($renderer) . 'View'), Theme :: get_image_path() . 'view_' . $renderer . '.png', $this->get_url(array(
                            RepositoryManager :: PARAM_RENDERER => $renderer)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }
            
            $this->action_bar->add_tool_action(new ToolbarItem(Translation :: get('ExportEntireRepository'), Theme :: get_common_image_path() . 'action_backup.png', $this->get_url(array(
                    Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => 'all')), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $this->action_bar;
    }

    public function get_condition()
    {
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->get_parent_id());
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());

        $types = array(LearningPathItem :: get_type_name(), PortfolioItem :: get_type_name());

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

}
?>