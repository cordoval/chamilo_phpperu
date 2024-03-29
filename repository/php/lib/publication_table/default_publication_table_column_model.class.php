<?php
namespace repository;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * $Id: default_publication_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.publication_table
 */

/**
 * TODO: Add comment
 */
class DefaultPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
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
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, false);
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_APPLICATION, false);
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_LOCATION, false);
        $columns[] = new ObjectTableColumn(ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE, false);
        return $columns;
    }
}
?>