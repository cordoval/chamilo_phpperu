<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipOrganizerMomentTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMomentTableCellRenderer()
    {
    }

    function render_cell($column, $moment)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerMoment :: PROPERTY_NAME :
                return $moment->get_name();
            case InternshipOrganizerMoment :: PROPERTY_DESCRIPTION :
                return $moment->get_description();
            case InternshipOrganizerMoment :: PROPERTY_BEGIN :
                return $this->get_date($moment->get_begin());
             case InternshipOrganizerMoment :: PROPERTY_END :
                return $this->get_date($moment->get_end());
            default :
                return '&nbsp;';
        }
    }
	
	private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        
        }
    }
    
    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>