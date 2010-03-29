<?php
/**
 * $Id: default_category_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_table
 */
/**
 * TODO: Add comment
 */
class DefaultInternshipPlannerCategoryTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerCategoryTableCellRenderer()
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
            case InternshipPlannerCategory :: PROPERTY_ID :
                return $category->get_id();
            case InternshipPlannerCategory :: PROPERTY_NAME :
                return $category->get_name();
            case InternshipPlannerCategory :: PROPERTY_DESCRIPTION :
                return $category->get_description();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>