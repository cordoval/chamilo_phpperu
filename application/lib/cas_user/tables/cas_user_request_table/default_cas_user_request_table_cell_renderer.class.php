<?php

require_once dirname(__FILE__) . '/../../cas_user_request.class.php';

/**
 * @author Hans De Bisschop
 */
class DefaultCasUserRequestTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCasUserRequestTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param CasUserRequest $cas_user_request
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $cas_user_request)
    {
        switch ($column->get_name())
        {
            case CasUserRequest :: PROPERTY_ID :
                return $cas_user_request->get_id();
            case CasUserRequest :: PROPERTY_FIRST_NAME :
                return $cas_user_request->get_first_name();
            case CasUserRequest :: PROPERTY_LAST_NAME :
                return $cas_user_request->get_last_name();
            case CasUserRequest :: PROPERTY_EMAIL :
                return $cas_user_request->get_email();
            case CasUserRequest :: PROPERTY_AFFILIATION :
                return $cas_user_request->get_affiliation();
            case CasUserRequest :: PROPERTY_MOTIVATION :
                return $cas_user_request->get_motivation();
            case CasUserRequest :: PROPERTY_REQUESTER_ID :
                return $cas_user_request->get_requester_id();
            case CasUserRequest :: PROPERTY_REQUEST_DATE :
                return DatetimeUtilities :: format_locale_date(null, $cas_user_request->get_request_date());
            case CasUserRequest :: PROPERTY_STATUS :
                return $cas_user_request->get_status();
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