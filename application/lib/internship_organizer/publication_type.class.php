<?php

class InternshipOrganizerPublicationType
{
    
    const CONTRACT = 1;
    const PREPARATION = 2;
    const EVALUATION = 3;

    static function get_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerContract');
        $names[2] = Translation :: get('InternshipOrganizerPreparation');
        $names[3] = Translation :: get('InternshipOrganizerEvaluation');
        return $names;
    }

    static function get_user_type_name($index)
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
            default :
                //no default
                break;
        }
    
    }

}