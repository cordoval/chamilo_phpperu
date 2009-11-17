<?php
/**
 * $Id: default_category_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.category_table
 */

require_once dirname(__FILE__) . '/../../item.class.php';
/**
 * TODO: Add comment
 */
class DefaultCategoryTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCategoryTableCellRenderer($browser)
    {
    
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $category)
    {
        switch ($column->get_name())
        {
            case Category :: PROPERTY_ID :
                return $category->get_id();
            case Category :: PROPERTY_NAME :
                $url = $this->browser->get_browse_categories_url($category->get_id());
                return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
            case Category :: PROPERTY_POOL :
                if ($category->use_as_pool())
                    return Translation :: get('Yes');
                else
                    return Translation :: get('No');
        }
        
        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_common_image_path() . ('treemenu_types/category.png');
            return '<img src="' . $img . '"alt="category" />';
        }
        
        return '&nbsp;';
    }

    function render_id_cell($category)
    {
        return $category->get_id();
    }
}
?>