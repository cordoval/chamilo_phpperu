<?php
/**
 * $Id: group_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.group_browser
 */
require_once dirname(__FILE__) . '/group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../group_table/default_group_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function GroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === GroupBrowserTableColumnModel :: get_modification_column())
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
                return '<a href="' . htmlentities($this->browser->get_group_viewing_url($group)) . '" title="' . $title . '">' . $title_short . '</a>';
            case Group :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $group));
                //				if(strlen($description) > 175)
                //				{
                //					$description = mb_substr($description,0,170).'&hellip;';
                //				}
                return Utilities :: truncate_string($description);
            case Translation :: get('Users') :
                return $group->count_users(true, true);
            case Translation :: get('Subgroups') :
                return $group->count_subgroups(true, true);
        }
        
        return parent :: render_cell($column, $group);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($group)
    {
        
        $toolbar = new Toolbar();
    	
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', 
					$this->browser->get_group_editing_url($group), ToolbarItem :: DISPLAY_ICON));
					
		$toolbar->add_item(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path().'action_subscribe.png', 
					$this->browser->get_group_suscribe_user_browser_url($group), ToolbarItem :: DISPLAY_ICON));
        
        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
        $users = $this->browser->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);
        
        if ($visible)
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Truncate'), 
        			Theme :: get_common_image_path().'action_recycle_bin.png', 
					$this->browser->get_group_emptying_url($group), 
					ToolbarItem :: DISPLAY_ICON, 
					true
			));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('TruncateNA'),
        			Theme :: get_common_image_path().'action_recycle_bin_na.png', 
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_group_delete_url($group),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
		));
			
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Move'),
        			Theme :: get_common_image_path().'action_move.png', 
					$this->browser->get_move_group_url($group),
				 	ToolbarItem :: DISPLAY_ICON
		));
    }
}
?>