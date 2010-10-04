<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';

class DefaultExternalRepositoryInstanceTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultExternalRepositoryInstanceTableColumnModel()
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
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>