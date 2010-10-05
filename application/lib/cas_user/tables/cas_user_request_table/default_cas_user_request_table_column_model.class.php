<?php
require_once dirname(__FILE__) . '/../../cas_user_request.class.php';

/**
 * @author Hans De Bisschop
 */
class DefaultCasUserRequestTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCasUserRequestTableColumnModel()
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

        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_FIRST_NAME);
        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_LAST_NAME);
        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_EMAIL);
        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_REQUESTER_ID, false);
        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_REQUEST_DATE);
        $columns[] = new ObjectTableColumn(CasUserRequest :: PROPERTY_STATUS);
        return $columns;
    }
}
?>