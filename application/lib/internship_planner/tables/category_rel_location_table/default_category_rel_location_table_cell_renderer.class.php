<?php
/**
 * $Id: default_category_rel_location_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipPlannerCategoryRelLocationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerCategoryRelLocationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $categoryrellocation)
    {
        switch ($column->get_name())
        {
            case InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID :
                return $categoryrellocation->get_location_id();
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