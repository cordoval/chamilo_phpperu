<?php
/**
 * $Id: category_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.category_browser
 */
require_once dirname(__FILE__) . '/category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/category_table/default_category_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../category.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CategoryBrowserTableCellRenderer extends DefaultCategoryTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;
    private $count;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function CategoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->count = $browser->count_categories($browser->get_condition());
    }

    // Inherited
    function render_cell($column, $category)
    {
        if ($column === CategoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($category);
        }
        
        return parent :: render_cell($column, $category);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($category)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_category_url($category->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_category_url($category->get_id()),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('ModifyRights'),
        		Theme :: get_common_image_path() . 'action_rights.png',
        		$this->browser->get_modify_rights_url('category', $category->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('ManageQuota'),
        		Theme :: get_common_image_path() . 'action_statistics.png',
        		$this->browser->get_browse_category_quota_boxes_url($category->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
    	if ($category->get_display_order() > 1)
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('MoveUp'),
	        		Theme :: get_common_image_path() . 'action_up.png',
	        		$this->browser->get_move_category_url($category->get_id(), - 1),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('MoveUpNA'),
	        		Theme :: get_common_image_path() . 'action_up_na.png',
	        		null,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        
        if ($category->get_display_order() < $this->count)
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('MoveDown'),
	        		Theme :: get_common_image_path() . 'action_down.png',
	        		$this->browser->get_move_category_url($category->get_id(), 1),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('MoveDownNA'),
	        		Theme :: get_common_image_path() . 'action_down_na.png',
	        		null,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        
        return $toolbar->as_html();
        
    }
}
?>