<?php

require_once dirname(__FILE__) . '/../../organisation.class.php';

class DefaultInternshipPlannerOrganisationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerOrganisationTableCellRenderer()
    {
    }

    function render_cell($column, $organisation)
    {
        
        switch ($column->get_name())
        {
            case InternshipPlannerOrganisation :: PROPERTY_NAME :
                return $organisation->get_name();
            case InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION :
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