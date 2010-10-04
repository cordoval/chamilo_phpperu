<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipOrganizerMomentRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMomentRelUserTableCellRenderer()
    {
    }

    function render_cell($column, $moment)
    {
//        dump($moment);
        
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
            case User :: PROPERTY_FIRSTNAME :
                return $moment->get_optional_property(User :: PROPERTY_FIRSTNAME);
            case User :: PROPERTY_LASTNAME :
                return $moment->get_optional_property(User :: PROPERTY_LASTNAME);
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