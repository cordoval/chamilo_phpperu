<?php
namespace user;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_user_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */

class DefaultUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultUserTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return UserTableColumn[]
     */
    private static function get_default_columns()
    {
    	$udm = UserDataManager :: get_instance();
        $user_alias = $udm->get_alias(User :: get_table_name());

        $columns = array();
        //$columns[] = new ObjectTableColumn(User :: PROPERTY_PICTURE_URI);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_OFFICIAL_CODE, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);

        return $columns;
    }
}
?>