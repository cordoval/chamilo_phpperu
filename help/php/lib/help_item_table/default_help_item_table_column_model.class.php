<?php
namespace help;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_help_item_table_column_model.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_item_table
 */

/**
 * TODO: Add comment
 */
class DefaultHelpItemTableColumnModel extends ObjectTableColumnModel
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
        $columns[] = new ObjectTableColumn(HelpItem :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(HelpItem :: PROPERTY_LANGUAGE);
        $columns[] = new ObjectTableColumn(HelpItem :: PROPERTY_URL);
        return $columns;
    }
}
?>