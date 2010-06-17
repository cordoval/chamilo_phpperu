<?php
/**
 * $Id: default_help_item_table_cell_renderer.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_item_table
 */

/**
 * TODO: Add comment
 */
class DefaultHelpItemTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultHelpItemTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $help_item)
    {
        switch ($column->get_name())
        {
            case HelpItem :: PROPERTY_NAME :
                return $help_item->get_name();
            case HelpItem :: PROPERTY_LANGUAGE :
                return $help_item->get_language();
            case HelpItem :: PROPERTY_URL :
                return $help_item->get_url();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($help_item)
    {
        return $help_item->get_id();
    }
}
?>