<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';

class DefaultExternalRepositoryObjectTableColumnModel extends ObjectTableColumnModel
{

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
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject :: PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject :: PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject :: PROPERTY_CREATED);
        return $columns;
    }
}
?>