<?php
/**
 * $Id: default_user_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */

class DefaultInternshipOrganizerUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerUserTableColumnModel()
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
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        
        
        
        return $columns;
    }
}
?>