<?php
require_once dirname(__FILE__) . '/../../cas_account.class.php';

/**
 * @author Hans De Bisschop
 */
class DefaultCasAccountTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCasAccountTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $columns = array();

        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_FIRST_NAME);
        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_LAST_NAME);
        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_EMAIL);
        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_AFFILIATION);
        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_GROUP);
        $columns[] = new ObjectTableColumn(CasAccount :: PROPERTY_STATUS);
        return $columns;
    }
}
?>