<?php

require_once dirname(__FILE__) . '/../../cas_user_request.class.php';

/**
 * @author Hans De Bisschop
 */
class DefaultCasAccountTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCasAccountTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param CasUserRequest $cas_user_request
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $cas_account)
    {
        switch ($column->get_name())
        {
            case CasAccount :: PROPERTY_ID :
                return $cas_account->get_id();
            case CasAccount :: PROPERTY_FIRST_NAME :
                return $cas_account->get_first_name();
            case CasAccount :: PROPERTY_LAST_NAME :
                return $cas_account->get_last_name();
            case CasAccount :: PROPERTY_EMAIL :
                return $cas_account->get_email();
            case CasAccount :: PROPERTY_AFFILIATION :
                return $cas_account->get_affiliation();
            case CasAccount :: PROPERTY_GROUP :
                return $cas_account->get_group();
            case CasAccount :: PROPERTY_STATUS :
                return $cas_account->get_status();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>