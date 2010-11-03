<?php
use common\libraries\WebApplication;
use common\libraries\CoreApplication;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
require_once Path :: get_common_libraries_class_path() . 'utilities.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer/php/mentor.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer/php/mentor_rel_location.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer/php/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $location_id =  $_GET[InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID];
    $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelLocation ::PROPERTY_LOCATION_ID, $location_id);
    
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, InternshipOrganizerMentor :: PROPERTY_LASTNAME, InternshipOrganizerMentor :: PROPERTY_TITLE, InternshipOrganizerMentor :: PROPERTY_EMAIL));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }
    
    if (count($conditions) > 0)
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }
      
    $dm = InternshipOrganizerDataManager :: get_instance();
    $objects = $dm->retrieve_mentor_rel_locations($condition);
    
    while ($mentor = $objects->next_result())
    {
        $mentors[] = $mentor;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($mentors);

echo '</tree>';

function dump_tree($mentors)
{
    if (contains_results($mentors))
    {
        echo '<node id="0" classes="category unlinked" title="' . Translation :: get('InternshipOrganizerMentors') . '">' . "\n";
        
        foreach ($mentors as $mentor)
        {
            $id = 'mentor_' . $mentor->get_mentor_id();
            $name = strip_tags($mentor->get_optional_property(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME ).' '.$mentor->get_optional_property(InternshipOrganizerMentor :: PROPERTY_LASTNAME ));
            $description = strip_tags($mentor->get_optional_property(InternshipOrganizerMentor :: PROPERTY_TITLE ).' '.$mentor->get_optional_property(InternshipOrganizerMentor :: PROPERTY_EMAIL ));
            $description = preg_replace("/[\n\r]/", "", $description);
            
            echo '<leaf id="' . $id . '" classes="' . '' . '" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $name) . '"/>' . "\n";
        }
        
        echo '</node>' . "\n";
    
    }
}

function contains_results($objects)
{
    if (count($objects))
    {
        return true;
    }
    return false;
}
?>