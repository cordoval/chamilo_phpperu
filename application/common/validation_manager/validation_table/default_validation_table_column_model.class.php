<?php
/**
 * $Id: default_validation_table_column_model.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component.validation_table
 */

class DefaultValidationTableColumnMod extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultValidationTableColumnMod()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ValidationTableColumn[]
     */
    private static function get_default_columns()
    {
        $udm = UserDataManager :: get_instance();
        $user_alias = $udm->get_alias(User :: get_table_name());

        $columns = array();

        // TODO: Make this work by refactoring JOIN statements.
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true, User :: get_table_name());
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, User :: get_table_name());
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, User :: get_table_name());
        $columns[] = new ObjectTableColumn(Validation :: PROPERTY_VALIDATED, true);
        return $columns;
    }
}
?>