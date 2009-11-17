<?php
/**
 * $Id: shared_content_objects_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerSharedContentObjectsBrowserComponent extends RepositoryManagerComponent
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SharedObjects')));
        $trail->add_help('repository general');
        
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
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . Utilities :: underscores_to_camelcase(($session_filter))));
        }
        
        $this->display_header($trail, false, true);
        
        echo $this->action_bar->as_html();
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
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $table = new RepositorySharedContentObjectsBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    function has_right($content_object_id, $right)
    {
        foreach ($this->list as $key => $value)
        {
            if ($value['content_object'] == $content_object_id && $value['right'] == $right)
                return true;
        }
        return false;
    }

    private function get_condition()
    {
        //TODO: limit this so only the shared objects are seen (view and use)
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
            $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
            
            $conditions[] = new OrCondition($or_conditions);
        }
        
        $cond = $this->form->get_filter_conditions();
        if ($cond)
        {
            $conditions[] = $cond;
        }
        
        $rdm = RightsDataManager :: get_instance();
        
        $user = $this->get_user();
        $groups = $user->get_groups();
        foreach ($groups as $group)
        {
            $group_ids[] = $group->get_id();
        }
        
        //retrieve all the rights
        $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
        $rights_db = $reflect->getConstants();
        
        foreach ($rights_db as $right_id)
        {
            if ($right_id != RepositoryRights :: VIEW_RIGHT && $right_id != RepositoryRights :: USE_RIGHT && $right_id != RepositoryRights :: REUSE_RIGHT)
                continue;
            $rights[] = $right_id;
        }
        
        $location_ids = array();
        $shared_content_objects = $rdm->retrieve_shared_content_objects_for_user($user->get_id(), $rights);
        
        while ($user_right_location = $shared_content_objects->next_result())
        {
            if (! in_array($user_right_location->get_location_id(), $location_ids))
                $location_ids[] = $user_right_location->get_location_id();
            
            $this->list[] = array('location_id' => $user_right_location->get_location_id(), 'user' => $user_right_location->get_user_id(), 'right' => $user_right_location->get_right_id());
        }
        
        $shared_content_objects = $rdm->retrieve_shared_content_objects_for_groups($group_ids, $rights);
        
        while ($group_right_location = $shared_content_objects->next_result())
        {
            if (! in_array($group_right_location->get_location_id(), $location_ids))
                $location_ids[] = $group_right_location->get_location_id();
            
            $this->list[] = array('location_id' => $group_right_location->get_location_id(), 'group' => $group_right_location->get_group_id(), 'right' => $group_right_location->get_right_id());
        }
        
        if (count($location_ids) > 0)
        {
            $location_cond = new InCondition('id', $location_ids);
            $locations = $rdm->retrieve_locations($location_cond);
            
            while ($location = $locations->next_result())
            {
                $ids[] = $location->get_identifier();
                
                foreach ($this->list as $key => $value)
                {
                    if ($value['location_id'] == $location->get_id())
                    {
                        $value['content_object'] = $location->get_identifier();
                        $this->list[$key] = $value;
                    }
                }
            }
            
            if ($ids)
                $conditions[] = new InCondition('id', $ids, ContentObject :: get_table_name());
            
            if ($conditions)
                $condition = new AndCondition($conditions);
        }
        
        if (! $condition)
        {
            $condition = new EqualityCondition('id', - 1, ContentObject :: get_table_name());
        }
        
        return $condition;
    }

}
?>
