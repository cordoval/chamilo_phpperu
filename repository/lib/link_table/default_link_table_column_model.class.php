<?php
/**
 * $Id: default_link_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.link_table
 */

/**
 * TODO: Add comment
 */
class DefaultLinkTableColumnModel extends ObjectTableColumnModel
{

	private $type;

    /**
     * Constructor
     */
    function DefaultLinkTableColumnModel($type)
    {
        parent :: __construct(self :: get_default_columns(), 3);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_APPLICATION);
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_LOCATION);
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE);
        return $columns;
    }
}
?>