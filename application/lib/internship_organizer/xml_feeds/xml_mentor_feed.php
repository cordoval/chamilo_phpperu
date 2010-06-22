<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/mentor.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $organisation_id =  $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
    $conditions[] = new EqualityCondition(InternshipOrganizerMentor::PROPERTY_ORGANISATION_ID, $organisation_id);
    
    
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
    $objects = $dm->retrieve_mentors($condition);
    
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
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Periods'), '">', "\n";
        
        foreach ($mentors as $mentor)
        {
            $id = $mentor->get_id();
            $name = strip_tags($mentor->get_firstname().' '.$mentor->get_lastname());
            $description = strip_tags($mentor->get_title().' '.$mentor->get_email());
            $description = preg_replace("/[\n\r]/", "", $description);
            
            echo '<leaf id="', $id, '" classes="', '', '" title="', htmlspecialchars($name), '" description="', htmlspecialchars(isset($description) && ! empty($description) ? $description : $name), '"/>', "\n";
        }
        
        echo '</node>', "\n";
    
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