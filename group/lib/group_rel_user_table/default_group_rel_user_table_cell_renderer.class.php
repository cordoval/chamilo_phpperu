<?php
/**
 * $Id: default_group_rel_user_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_rel_user_table
 */

/**
 * TODO: Add comment
 */
class DefaultGroupRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultGroupRelUserTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $groupreluser)
    {
        switch ($column->get_name())
        {
            case GroupRelUser :: PROPERTY_USER_ID :
                return $groupreluser->get_user_id();
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