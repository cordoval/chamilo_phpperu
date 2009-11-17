<?php
/**
 * $Id: category_browser_table_cell_renderer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component.category_browser
 */
require_once dirname(__FILE__) . '/category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../platform_category.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CategoryBrowserTableCellRenderer implements ObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;
    private $count;
    
    private $count_all;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function CategoryBrowserTableCellRenderer($browser)
    {
        //parent :: __construct();
        $this->browser = $browser;
        $this->count = $browser->count_categories($browser->get_condition());
        $this->count_all = $browser->count_categories();
    }

    // Inherited
    function render_cell($column, $category)
    {
        if ($column === CategoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($category);
        }
        
        switch ($column->get_name())
        {
            case PlatformCategory :: PROPERTY_ID :
                return $category->get_id();
            case PlatformCategory :: PROPERTY_NAME :
                $url = $this->browser->get_browse_categories_url($category->get_id());
                return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
        }
        
        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_common_image_path() . 'treemenu_types/category.png';
            return '<img src="' . $img . '"alt="category" />';
        }
        
        if ($title == Translation :: get('Subcategories'))
        {
            $count = $this->browser->count_categories(new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $category->get_id()));
            return $count;
        }
        
        return '&nbsp;';
    
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
    private function get_modification_links($category)
    {
        $toolbar_data = array();
        
        if ($this->browser->allowed_to_edit_category($category->get_id()))
        {
            $toolbar_data[] = array('href' => $this->browser->get_update_category_url($category->get_id()), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit_na.png');
        }
        
        if ($this->browser->allowed_to_delete_category($category->get_id()))
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_category_url($category->get_id()), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete_na.png');
        }
        
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
        
        if ($this->count_all > 1)
        {
            $toolbar_data[] = array('href' => $this->browser->get_change_category_parent_url($category->get_id()), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveNA'), 'img' => Theme :: get_common_image_path() . 'action_move_na.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>