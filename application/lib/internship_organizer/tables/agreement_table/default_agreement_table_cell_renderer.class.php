<?php

require_once dirname(__FILE__) . '/../../agreement.class.php';

class DefaultInternshipOrganizerAgreementTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerAgreementTableCellRenderer()
    {
    }

    function render_cell($column, $agreement)
    {
        $user = UserDataManager::get_instance()->retrieve_user($agreement->get_student_id());
    	$period = InternshipOrganizerDataManager::get_instance()->retrieve_period($agreement->get_period_id());
        
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
                return $user->get_firstname();
            case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();        
            case Translation :: get('InternshipOrganizerPeriodName') :
                return $period->get_name();  
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