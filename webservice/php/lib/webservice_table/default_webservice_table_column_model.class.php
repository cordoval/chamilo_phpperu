<?php
namespace webservice;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_webservice_table_column_model.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_table
 */

/**
 * TODO: Add comment
 */
class DefaultWebserviceTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
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
        $columns[] = new ObjectTableColumn(WebserviceRegistration :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(WebserviceRegistration :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>