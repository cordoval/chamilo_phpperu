<?php
namespace rights;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * @package rights.lib.tables.rights_template_table
 */

/**
 * TODO: Add comment
 */
class DefaultRightsTemplateTableColumnModel extends ObjectTableColumnModel
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
        $columns[] = new ObjectTableColumn(RightsTemplate :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(RightsTemplate :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>