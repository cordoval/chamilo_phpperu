<?php
/**
 * $Id: subscribe_group_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
require_once dirname(__FILE__) . '/subscribe_group_browser_table_column_model.class.php';
require_once Path :: get_group_path() . '/lib/group_table/default_group_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribeGroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SubscribeGroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === SubscribeGroupBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($group);
        }

        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case Group :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return $title_short;
            //return '<a href="'.htmlentities($this->browser->get_group_viewing_url($group)).'" title="'.$title.'">'.$title_short.'</a>';
            case Group :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $group));
                //				if(strlen($description) > 175)
                //				{
                //					$description = mb_substr($description,0,170).'&hellip;';
                //				}
                return Utilities :: truncate_string($description, 175);
            case Translation :: get('Users') :
                $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
                $count = GroupDataManager :: get_instance()->count_group_rel_users($condition);
                return $count;
            case Translation :: get('Subgroups') :
                $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $group->get_id());
                $count = GroupDataManager :: get_instance()->count_groups($condition);
                return $count;
        }

        return parent :: render_cell($column, $group);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links(Group $group)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $this->browser->get_course_id());
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $group->get_id());
        $condition = new AndCondition($conditions);
        
        $count = WeblcmsDataManager :: get_instance()->count_course_group_relations($condition);
        
        $parent_ids = array();
        $parents = $group->get_parents(false);
        while($parent = $parents->next_result())
        {
        	$parent_ids[] = $parent->get_id();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $this->browser->get_course_id());
        $conditions[] = new InCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $parent_ids);
        $condition = new AndCondition($conditions);
        
        $count_parents = WeblcmsDataManager :: get_instance()->count_course_group_relations($condition);
        
        if($count == 0)
        {
	    	if($count_parents == 0)
	    	{
	        	$parameters[Tool :: PARAM_ACTION] = UserTool :: ACTION_SUBSCRIBE_USERS_FROM_GROUP;
		        $parameters[UserTool :: PARAM_GROUPS] = $group->get_id();
		
		        $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('SubscribeUsersFromGroup'),
		        		Theme :: get_common_image_path() . 'action_copy.png',
		        		$this->browser->get_url($parameters),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
		        
		        $parameters[Tool :: PARAM_ACTION] = UserTool :: ACTION_SUBSCRIBE_GROUPS;
		        $parameters[UserTool :: PARAM_GROUPS] = $group->get_id();
		        
		        $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('SubscribeGroup'),
		        		Theme :: get_common_image_path() . 'action_subscribe.png',
		        		$this->browser->get_url($parameters),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
	    	}
	    	else
	    	{
	    		$toolbar->add_item(new ToolbarItem(
		        		Translation :: get('GroupSubscribedThroughParent'),
		        		Theme :: get_common_image_path() . 'action_setting_true_inherit.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
	    	}
        }
        else
        {
	        $parameters[Tool :: PARAM_ACTION] = UserTool :: ACTION_UNSUBSCRIBE_GROUPS;
	        $parameters[UserTool :: PARAM_GROUPS] = $group->get_id();
	        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('UnsubscribeGroup'),
	        		Theme :: get_common_image_path() . 'action_unsubscribe.png',
	        		$this->browser->get_url($parameters),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        
        return $toolbar->as_html();
    }
}
?>