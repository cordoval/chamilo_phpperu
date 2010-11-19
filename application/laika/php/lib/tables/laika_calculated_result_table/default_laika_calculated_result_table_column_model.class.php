<?php
namespace application\laika;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

use user\UserDataManager;
use user\User;
/**
 * $Id: default_laika_calculated_result_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.tables.laika_calculated_result_table
 */

/**
 * TODO: Add comment
 */
class DefaultLaikaCalculatedResultTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $attempt_alias = LaikaDataManager :: get_instance()->get_alias(LaikaAttempt :: get_table_name());

        $columns = array();
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_EMAIL, true, $user_alias);
        $columns[] = new ObjectTableColumn(LaikaAttempt :: PROPERTY_DATE, true, $attempt_alias);
        return $columns;
    }
}
?>