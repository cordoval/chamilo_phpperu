<?php

require_once dirname(__FILE__) . '/../../organisation.class.php';

class DefaultInternshipOrganizerOrganisationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerOrganisationTableCellRenderer()
    {
    }

    function render_cell($column, $organisation)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerOrganisation :: PROPERTY_NAME :
                return $organisation->get_name();
            case InternshipOrganizerOrganisation :: PROPERTY_ADDRESS :
                return $organisation->get_address();    
            /*case InternshipOrganizerOrganisation :: PROPERTY_POSTCODE :
                return $organisation->get_postcode();*/    
            case InternshipOrganizerOrganisation :: PROPERTY_CITY :
                return $organisation->get_postcode() . ' ' . $organisation->get_city();
            case InternshipOrganizerOrganisation :: PROPERTY_TELEPHONE :
                return $organisation->get_telephone();    
            case InternshipOrganizerOrganisation :: PROPERTY_FAX :
                return $organisation->get_fax();    
            case InternshipOrganizerOrganisation :: PROPERTY_EMAIL :
                return $organisation->get_email();    
            case InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($organisation->get_description(), 200);
                return $description;
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