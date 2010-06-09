<?php
/**
 * $Id: group_rel_user_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.group_rel_user_browser
 */
require_once dirname(__FILE__) . '/group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../group_rel_user_table/default_group_rel_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GroupRelUserBrowserTableCellRenderer extends DefaultGroupRelUserTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function GroupRelUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $groupreluser)
    {
        if ($column === GroupRelUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($groupreluser);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case GroupRelUser :: PROPERTY_USER_ID :
                $user_id = parent :: render_cell($column, $groupreluser);
                $user = UserManager :: retrieve_user($user_id);
                //				return '<a href="' . Path :: get(WEB_PATH) . 'index_user.php?go=view&id=' . $user->get_id() .
                //					'">' . $user->get_username() . '</a>';
                return $user->get_fullname();
        }
        return parent :: render_cell($column, $groupreluser);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($groupreluser)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Unsubscribe'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_group_rel_user_unsubscribing_url($groupreluser),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
		));
		        
        return $toolbar->as_html();
    }
}
?>