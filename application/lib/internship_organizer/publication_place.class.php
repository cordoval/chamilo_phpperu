<?php

class InternshipOrganizerPublicationPlace
{
    
    const PERIOD = 1;
    const AGREEMENT = 2;
    const MOMENT = 3;
    const LOCATION = 4;

    static function get_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerPeriod');
        $names[2] = Translation :: get('InternshipOrganizerAgreement');
        $names[3] = Translation :: get('InternshipOrganizerMoment');
        $names[4] = Translation :: get('InternshipOrganizerLocation');
        return $names;
    }

    static function get_user_type_name($index)
    {
        
        switch ($index)
        {
            case 1 :
                return Translation :: get('InternshipOrganizerPeriod');
            //                break;
            case 2 :
                return Translation :: get('InternshipOrganizerAgreement');
            //                break;
            case 3 :
                return Translation :: get('InternshipOrganizerMoment');
            //                break;
            case 4 :
                return Translation :: get('InternshipOrganizerLocation');
            //    
            

            default :
                //no default
                break;
        }
    
    }

}