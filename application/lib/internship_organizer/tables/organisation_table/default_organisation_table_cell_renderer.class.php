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
            	//$organisation_string = Utilities :: truncate_string($organisation->get_name(), 100);
                //return $organisation_string;
				return $organisation->get_name();
            	
            case InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION :
                //$description = Utilities :: truncate_string($organisation->get_description(), 100);
                //return $description;
                return $organisation->get_description();
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