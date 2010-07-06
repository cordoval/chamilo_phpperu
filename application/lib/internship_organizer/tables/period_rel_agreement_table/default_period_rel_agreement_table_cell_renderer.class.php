<?php

require_once dirname(__FILE__) . '/../../agreement.class.php';

class DefaultInternshipOrganizerPeriodRelAgreementTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerPeriodRelAgreementTableCellRenderer()
    {
    }

    function render_cell($column, $agreement)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerAgreement :: PROPERTY_NAME :
                return $agreement->get_name();
            case InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($agreement->get_description(), 200);
                return $description;
            case InternshipOrganizerAgreement :: PROPERTY_BEGIN :
                return $this->get_date($agreement->get_begin());
            case InternshipOrganizerAgreement :: PROPERTY_END :
                return $this->get_date($agreement->get_end());
            case User :: PROPERTY_FIRSTNAME :
                return $agreement->get_optional_property(User :: PROPERTY_FIRSTNAME);
            case User :: PROPERTY_LASTNAME :
                return  $agreement->get_optional_property(User :: PROPERTY_LASTNAME);
            case InternshipOrganizerAgreement :: PROPERTY_STATUS :
                return InternshipOrganizerAgreement :: get_status_name($agreement->get_status());
            default :
                return '&nbsp;';
        }
           
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }

    private function get_date($date)
    {
        return date("d-m-Y", $date);
    }
}
?>