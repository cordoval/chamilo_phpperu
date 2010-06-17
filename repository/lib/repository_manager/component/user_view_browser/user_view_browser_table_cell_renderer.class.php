<?php
/**
 * $Id: user_view_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.user_view_browser
 */
require_once dirname(__FILE__) . '/user_view_browser_table_column_model.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class UserViewBrowserTableCellRenderer extends ObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function UserViewBrowserTableCellRenderer($browser)
    {
        //parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user_view)
    {
        if ($column === UserViewBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user_view);
        }
        switch ($column->get_name())
        {
            case UserView :: PROPERTY_NAME :
                return $user_view->get_name();
            case UserView :: PROPERTY_DESCRIPTION :
                return strip_tags($user_view->get_description());
        }
    
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user_view)
    {
        $toolbar  = new Toolbar();
		$toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'), 
        		Theme :: get_common_image_path().'action_edit.png', 
				$this->browser->get_update_user_view_url($user_view->get_id()), 
				ToolbarItem :: DISPLAY_ICON
		));
       	$toolbar->add_item(new ToolbarItem(
       			Translation :: get('Remove'), 
       			Theme :: get_common_image_path().'action_delete.png', 
				$this->browser->get_delete_user_view_url($user_view->get_id()), 
				ToolbarItem :: DISPLAY_ICON, 
				true
		));
        
        return $toolbar->as_html();
    }
}
?>