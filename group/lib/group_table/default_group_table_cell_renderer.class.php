<?php
/**
 * $Id: default_group_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_table
 */
/**
 * TODO: Add comment
 */
class DefaultGroupTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultGroupTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $group)
    {
        switch ($column->get_name())
        {
            case Group :: PROPERTY_ID :
                return $group->get_id();
            case Group :: PROPERTY_NAME :
                return $group->get_name();
            case Group :: PROPERTY_DESCRIPTION :
                return $group->get_description();
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