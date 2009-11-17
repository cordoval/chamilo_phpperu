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
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_update_category_url($category->get_id()), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_delete_category_url($category->get_id()), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        
        $toolbar_data[] = array('href' => $this->browser->get_modify_rights_url('category', $category->get_id()), 'label' => Translation :: get('ModifyRights'), 'img' => Theme :: get_common_image_path() . 'action_rights.png');
        
        $url = $this->browser->get_browse_category_quota_boxes_url($category->get_id());
        $toolbar_data[] = array('href' => $url, 'label' => Translation :: get('ManageQuota'), 'img' => Theme :: get_common_image_path() . 'action_statistics.png');
        
        if ($category->get_display_order() > 1)
        {
            $toolbar_data[] = array('href' => $this->browser->get_move_category_url($category->get_id(), - 1), 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
        }
        
        if ($category->get_display_order() < $this->count)
        {
            $toolbar_data[] = array('href' => $this->browser->get_move_category_url($category->get_id(), 1), 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>