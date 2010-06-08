<?php
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
	const PARAM_VIEW_OBJECTS = 'view_objects';
	
    private $form;
	private $view;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$this->view = Request :: get(self :: PARAM_VIEW_OBJECTS);
		if(is_null($this->view)) $this->view = self :: VIEW_OTHERS_OBJECTS;
    	
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
        $condition = null;
        switch($this->view)
        {
        	case self :: VIEW_OTHERS_OBJECTS: 	$condition = $this->get_others_condition();
        										break;
        	case self :: VIEW_OWN_OBJECTS:		$condition = $this->get_own_condition();
        										break;
        }
        $parameters = $this->get_parameters(true);
        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new RepositorySharedContentObjectsBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        switch($this->view)
        {
        	case self :: VIEW_OTHERS_OBJECTS: 	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowOwnSharedObjects'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW_OBJECTS => self :: VIEW_OWN_OBJECTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        										break;
        	case self :: VIEW_OWN_OBJECTS:		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowOthersSharedObjects'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW_OBJECTS => self :: VIEW_OTHERS_OBJECTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        										break;
        }
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

    private function retrieve_rights()
    {
        //retrieve all the rights
        $rights = array();
        $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
        $rights_db = $reflect->getConstants();

        foreach ($rights_db as $right_id)
        {
            if ($right_id != RepositoryRights :: VIEW_RIGHT && $right_id != RepositoryRights :: USE_RIGHT && $right_id != RepositoryRights :: REUSE_RIGHT)
                continue;
            $rights[] = $right_id;
        }
        
        return $rights;
    }
    
    private function get_others_condition()
    {
        //TODO: limit this so only the shared objects are seen (view and use)
        $query = $this->action_bar->get_query();
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

        $rdm = RightsDataManager :: get_instance();

        $user = $this->get_user();
        $groups = $user->get_groups();
        foreach ($groups as $group)
        {
            $group_ids[] = $group->get_id();
        }

		$rights = $this->retrieve_rights();

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
    
	private function get_own_condition()
    {
        //TODO: limit this so only the shared objects are seen (view and use)
        $content_objects = $this->retrieve_content_objects(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id()));
        if($content_objects->size()>0)
        {
	        $ids = array();
	        while($content_object = $content_objects->next_result())
	        {
	        	$ids[] = $content_object->get_id();
	        }
	        
	        $rights_data_manager = RightsDataManager :: get_instance();
	        $locations = $rights_data_manager->retrieve_locations(new InCondition(Location :: PROPERTY_IDENTIFIER, $ids));
	        $location_ids = array();
	        while($location = $locations->next_result())
	        {
	        	$location_ids[$location->get_identifier()] = $location->get_id();
	        }
	        $rights = $this->retrieve_rights();
	        $rights_condition = new InCondition(UserRightLocation :: PROPERTY_RIGHT_ID, $rights);
	        $user_condition = new InCondition(UserRightLocation :: PROPERTY_LOCATION_ID, $location_ids);
	        $group_condition = new InCondition(GroupRightLocation :: PROPERTY_LOCATION_ID, $location_ids);
	        
	        $user_rights = $rights_data_manager->retrieve_user_right_locations(new AndCondition($rights_condition, $user_condition));
	        $group_rights = $rights_data_manager->retrieve_group_right_locations(new AndCondition($rights_condition, $group_condition));
	        
	        $ids = array();
	        while($user_right = $user_rights->next_result())
	        {
	        	foreach($location_ids as $index => $location_id)
	        	{
	        		if($user_right->get_location_id() == $location_id)
	        		{
	        			$ids[] = $index;
	        			unset($location_ids[$index]);
	        		}
	        	}	
	        }
	        
	        while($group_right = $group_rights->next_result())
	        {
	        	foreach($location_ids as $index => $location_id)
	        	{
	        		if($group_right->get_location_id() == $location_id)
	        		{
	        			$ids[] = $index;
	        			unset($location_ids[$index]);
	        		}
	        	}	
	        }
	        
	        if(count($ids))
	        	$condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
        }
        
        if($condition)
        	return $condition;
        else
        	return new EqualityCondition(ContentObject :: PROPERTY_ID, -1, ContentObject :: get_table_name());
    }

}
?>