<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/organisation_rel_user.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    $organisation_id =  $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
    $organisation_condition = new EqualityCondition(InternshipOrganizerOrganisationRelUser::PROPERTY_ORGANISATION_ID, $organisation_id);
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME, User :: PROPERTY_USERNAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(User :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }
    
    $organisation_id =  $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
    $organisation_condition = new EqualityCondition(InternshipOrganizerOrganisationRelUser::PROPERTY_ORGANISATION_ID, $organisation_id);
    
    $organisation_rel_users = InternshipOrganizerDataManager::get_instance()->retrieve_organisation_rel_users($organisation_condition);
 	$user_ids = array();
    while ($organisation_rel_user = $organisation_rel_users->next_result())
    {
        $user_ids[] = $organisation_rel_user->get_user_id();
    }
   	
    if(count($user_ids)){
    	$conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);
    }
    
    
    if (count($conditions) > 0)
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }
    
    $dm = UserDataManager :: get_instance();
    $objects = $dm->retrieve_users($condition);
    
    while ($user = $objects->next_result())
    {
        $users[] = $user;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($users);

echo '</tree>';

function dump_tree($users)
{
    if (contains_results($users))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Users'), '">', "\n";
        
        foreach ($users as $user)
        {
            $id = $user->get_id();
            $name = strip_tags($user->get_firstname().' '.$user->get_lastname());
//            $description = strip_tags($period->get_description());
//            $description = preg_replace("/[\n\r]/", "", $description);
            
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