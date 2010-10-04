<?php
/**
 * $Id: default_external_link_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.link_table
 */

/**
 * TODO: Add comment
 */
class DefaultExternalLinkTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultExternalLinkTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 3);
        $this->type = $type;
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_TITLE, false);
        $columns[] = new ObjectTableColumn(ExternalRepository :: PROPERTY_DESCRIPTION, false);        
        return $columns;
    }
}
?>