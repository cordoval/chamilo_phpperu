<?php
namespace repository;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

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
    function __construct()
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
        $columns[] = new ObjectTableColumn(ExternalInstance :: PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ExternalInstance :: PROPERTY_TITLE, false);
        $columns[] = new ObjectTableColumn(ExternalInstance :: PROPERTY_DESCRIPTION, false);
        return $columns;
    }
}
?>