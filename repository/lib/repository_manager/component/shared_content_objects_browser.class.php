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
	
    private $form;
	private $view;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$this->view = Request :: get(self :: PARAM_SHOW_OBJECTS_SHARED_BY_ME);
		if(is_null($this->view)) $this->view = self :: VIEW_OTHERS_OBJECTS;
    	
        $trail = BreadcrumbTrail :: get_instance();

        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());
        $output = $this->get_content_objects_html();
        
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
        	case self :: VIEW_OWN_OBJECTS:
        		$condition = $this->get_view_own_objects_condition();
        		break;
        	case self :: VIEW_OTHERS_OBJECTS:
        		 $condition = $this->get_view_other_objects_condition();
        		break;
        	default:
        		$condition = new EqualityCondition(ContentObject :: PROPERTY_ID, -1);
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
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }
    
    function get_view_own_objects_condition()
    {
    	$conditions = $subconditions = array();
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
    	
		$subconditions[] = new SubselectCondition(ContentObject :: PROPERTY_ID, ContentObjectUserShare :: PROPERTY_CONTENT_OBJECT_ID, ContentObjectUserShare :: get_table_name(), null, ContentObject :: get_table_name());
    	$subconditions[] = new SubselectCondition(ContentObject :: PROPERTY_ID, ContentObjectGroupShare :: PROPERTY_CONTENT_OBJECT_ID, ContentObjectGroupShare :: get_table_name(), null, ContentObject :: get_table_name());
		
		$conditions[] = new OrCondition($subconditions);
		return new AndCondition($conditions);
    }

    function get_view_other_objects_condition()
    {
    	$conditions = array();
    	
    	$user_sub_condition = new EqualityCondition(ContentObjectUserShare :: PROPERTY_USER_ID, $this->get_user_id(), ContentObjectUserShare :: get_table_name());
		$conditions[] = new SubselectCondition(ContentObject :: PROPERTY_ID, ContentObjectUserShare :: PROPERTY_CONTENT_OBJECT_ID, ContentObjectUserShare :: get_table_name(), $user_sub_condition, ContentObject :: get_table_name());
		
		$group_ids = array();
    	$groups = $this->get_user()->get_groups();
    	if($groups)
    	{
    		while($group = $groups->next_result())
    		{
    			$group_ids[] = $group->get_id();
    		}
    	
			$group_sub_condition = new InCondition(ContentObjectGroupShare :: PROPERTY_GROUP_ID, $group_ids, ContentObjectGroupShare :: get_table_name());
			$conditions[] = new SubselectCondition(ContentObject :: PROPERTY_ID, ContentObjectGroupShare :: PROPERTY_CONTENT_OBJECT_ID, ContentObjectGroupShare :: get_table_name(), $group_sub_condition, ContentObject :: get_table_name());
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

}
?>