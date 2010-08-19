<?php
/**
 * @package rights.lib.tables.type_template_table
 */

/**
 * TODO: Add comment
 */
class DefaultTypeTemplateTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultTypeTemplateTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(TypeTemplate :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(TypeTemplate :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>