<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipOrganizerMomentRelLocationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMomentRelLocationTableCellRenderer()
    {
    }

    function render_cell($column, $moment)
    {
//                dump($moment);
        

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
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $moment->get_optional_property(InternshipOrganizerLocation :: PROPERTY_ADDRESS);
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $moment->get_optional_property(InternshipOrganizerLocation :: PROPERTY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_TELEPHONE :
                return $moment->get_optional_property(InternshipOrganizerLocation :: PROPERTY_TELEPHONE);
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                return $moment->get_optional_property(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
                return $moment->get_optional_property(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);
            case InternshipOrganizerAgreement :: PROPERTY_NAME :
                return $moment->get_optional_property(InternshipOrganizerAgreement :: PROPERTY_NAME);
            case InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION :
                return $moment->get_optional_property(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION);
            case InternshipOrganizerPeriod :: PROPERTY_NAME :
                return $moment->get_optional_property(InternshipOrganizerPeriod :: PROPERTY_NAME);     
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
        return get_optional_property('moment_id');
    }
}
?>