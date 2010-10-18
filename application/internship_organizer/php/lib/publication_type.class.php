<?php

class InternshipOrganizerPublicationType
{
    
    const CONTRACT = 1;
    const PREPARATION = 2;
    const EVALUATION = 3;
    const INFO = 4;
    const GENERAL = 5;

    static function get_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerContract');
        $names[2] = Translation :: get('InternshipOrganizerPreparation');
        $names[3] = Translation :: get('InternshipOrganizerEvaluation');
        $names[4] = Translation :: get('InternshipOrganizerInfo');
        $names[5] = Translation :: get('InternshipOrganizerGeneral');
        
        return $names;
    }

    static function get_publication_type_name($index)
    {
        
        switch ($index)
        {
            case 1 :
                return Translation :: get('InternshipOrganizerContract');
            //                break;
            case 2 :
                return Translation :: get('InternshipOrganizerPreparation');
            //                break;
            case 3 :
                return Translation :: get('InternshipOrganizerEvaluation');
            //                break;
            case 4 :
                return Translation :: get('InternshipOrganizerInfo');
            //   
            case 5 :
                return Translation :: get('InternshipOrganizerGeneral');
            //   
            default :
                //no default
                break;
        }
    
    }

}