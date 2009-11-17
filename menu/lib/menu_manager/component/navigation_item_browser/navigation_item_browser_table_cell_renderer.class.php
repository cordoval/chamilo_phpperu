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
        
        $toolbar_data = array();
        $edit_url = $this->browser->get_navigation_item_editing_url($menu);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        if ($index == 'first' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
        }
        else
        {
            $move_url = $this->browser->get_navigation_item_moving_url($menu, 'up');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }
        else
        {
            $move_url = $this->browser->get_navigation_item_moving_url($menu, 'down');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        
        $delete_url = $this->browser->get_navigation_item_deleting_url($menu);
        $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>