<?php

require_once dirname(__FILE__) . '/../../organisation.class.php';

class DefaultInternshipOrganisationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganisationTableCellRenderer()
    {
    }

    function render_cell($column, $organisation)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganisation :: PROPERTY_NAME :
                return $organisation->get_name();
            case InternshipOrganisation :: PROPERTY_DESCRIPTION :
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