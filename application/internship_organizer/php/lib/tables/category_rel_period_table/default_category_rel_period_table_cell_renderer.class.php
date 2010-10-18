<?php
/**
 * $Id: default_category_rel_period_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_period_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryRelPeriodTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryRelPeriodTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $category_rel_period)
    {
        switch ($column->get_name())
        {
            case InternshipOrganizerCategory :: PROPERTY_NAME :
                return $category_rel_period->get_optional_property(InternshipOrganizerCategory :: PROPERTY_NAME);
            case InternshipOrganizerCategory :: PROPERTY_DESCRIPTION :
                return $category_rel_period->get_optional_property(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION);
            
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($category_rel_period)
    {
        return $category_rel_period->get_category_id().'|'.$category_rel_period->get_period_id();
    }
}
?>