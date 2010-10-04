<?php
/**
 * @package rights.lib.tables.type_template_table
 */
/**
 * TODO: Add comment
 */
class DefaultTypeTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultTypeTemplateTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $type_template)
    {
        switch ($column->get_name())
        {
            case TypeTemplate :: PROPERTY_NAME :
                return $type_template->get_name();
            case TypeTemplate :: PROPERTY_DESCRIPTION :
                $description = strip_tags($type_template->get_description());
                return Utilities :: truncate_string($description, 203);
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