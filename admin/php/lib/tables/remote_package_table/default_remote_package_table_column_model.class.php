<?php
namespace admin;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_remote_package_table_column_model.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.tables.remote_package_table
 */

/**
 * TODO: Add comment
 */
class DefaultRemotePackageTableColumnModel extends ObjectTableColumnModel
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
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_SECTION);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_VERSION);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_CYCLE_REALM);
        $columns[] = new ObjectTableColumn(RemotePackage :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>