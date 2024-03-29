<?php
namespace application\internship_organizer;

use common\libraries\Translation;

class InternshipOrganizerUserType
{
    
    const COORDINATOR = 1;
    const STUDENT = 2;
    const COACH = 3;
    const MENTOR = 4;

    static function get_period_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerCoordinator');
        $names[2] = Translation :: get('InternshipOrganizerStudent');
        $names[3] = Translation :: get('InternshipOrganizerCoach');
        return $names;
    }

    static function get_agreement_user_type_names()
    {
        $names = array();
        $names[1] = Translation :: get('InternshipOrganizerCoordinator');
        $names[2] = Translation :: get('InternshipOrganizerStudent');
        $names[3] = Translation :: get('InternshipOrganizerCoach');
        $names[4] = Translation :: get('InternshipOrganizerMentor');
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
                return Translation :: get('InternshipOrganizerCoach');
            //                break;
            default :
                //no default
                break;
        }
    
    }

    static function get_user_types()
    {
        return array(self :: COACH, self :: COORDINATOR, self :: STUDENT);
    }
}