<?php
/**
 * $Id: navigation_item_browser_table_cell_renderer.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component.navigation_item_browser
 */
require_once dirname(__FILE__) . '/navigation_item_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../navigation_item_table/default_navigation_item_table_cell_renderer.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class NavigationItemBrowserTableCellRenderer extends DefaultNavigationItemTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param MenuManagerManagerBrowserComponent $browser
     */
    function NavigationItemBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $menu)
    {
        if ($column === NavigationItemBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($menu);
        }
        return parent :: render_cell($column, $menu);
    }

    /**
     * Gets the action links to display
     * @param Object $menu The menu object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($menu)
    {
        $order = $menu->get_sort();
        $max = $this->browser->count_navigation_items($this->browser->get_condition());
        
        if ($max == 1)
        {
            $index = 'single';
        }
        else
        {
            if ($order == 1)
            {
                $index = 'first';
            }
            else
            {
                if ($order == $max)
                {
                    $index = 'last';
                }
                else
                {
                    $index = 'middle';
                }
            }
        }
        
        $toolbar = new Toolbar();
        
        $toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->browser->get_navigation_item_editing_url($menu),
    			ToolbarItem :: DISPLAY_ICON
    	));
    	
        if ($index == 'first' || $index == 'single')
        {
           	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('MoveUpNA'),
    			Theme :: get_common_image_path() . 'action_up_na.png',
    			null,
    			ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    			Translation :: get('MoveUp'),
    			Theme :: get_common_image_path() . 'action_up.png',
    			$this->browser->get_navigation_item_moving_url($menu, 'up'),
    			ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar->add_item(new ToolbarItem(
    			Translation :: get('MoveDownNA'),
    			Theme :: get_common_image_path() . 'action_down_na.png',
    			null,
    			ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    			Translation :: get('MoveDown'),
    			Theme :: get_common_image_path() . 'action_down.png',
    			$this->browser->get_navigation_item_moving_url($menu, 'down'),
    			ToolbarItem :: DISPLAY_ICON
    		));
        }

        $toolbar->add_item(new ToolbarItem(
    			Translation :: get('Delete'),
    			Theme :: get_common_image_path() . 'action_delete.png',
    			$this->browser->get_navigation_item_deleting_url($menu),
    			ToolbarItem :: DISPLAY_ICON,
    			true
    	));
        
        return $toolbar->as_html();
    }
}
?>