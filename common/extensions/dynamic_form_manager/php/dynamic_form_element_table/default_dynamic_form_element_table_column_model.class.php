<?php
namespace common\extensions\dynamic_form_manager;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * $Id: default_user_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */

class DefaultDynamicFormElementTableColumnModel extends ObjectTableColumnModel
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
     * @return UserTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_REQUIRED);
        return $columns;
    }
}
?>