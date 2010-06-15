<?php

class InternshipOrganizerUserType
{
    
    const COORDINATOR = 1;
    const STUDENT = 2;
    const MENTOR = 3;
    const COACH = 4;

    static function get_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerCoordinator');
        $names[2] = Translation :: get('InternshipOrganizerStudent');
        $names[3] = Translation :: get('InternshipOrganizerMentor');
        $names[4] = Translation :: get('InternshipOrganizerCoach');
        return $names;
    }

    static function get_user_type_name($index)
    {
        
    	    	
        switch ($index)
        {
            case 1 :
                return Translation :: get('InternshipOrganizerCoordinator');
//                break;
            case 2 :
                return Translation :: get('InternshipOrganizerStudent');
//                break;
            case 3 :
                return Translation :: get('InternshipOrganizerMentor');
//                break;
            case 4 :
                return Translation :: get('InternshipOrganizerCoach');
//                break;
            default :
                //no default
                break;
        }
    
    }
    
    
}