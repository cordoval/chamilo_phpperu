<?php

require_once dirname(__FILE__) . '/../../agreement.class.php';

class DefaultInternshipPlannerAgreementTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerAgreementTableCellRenderer()
    {
    }

    function render_cell($column, $agreement)
    {
        
        switch ($column->get_name())
        {
            case InternshipPlannerAgreement :: PROPERTY_NAME :
                return $agreement->get_name();
            case InternshipPlannerAgreement :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($agreement->get_description(), 200);
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