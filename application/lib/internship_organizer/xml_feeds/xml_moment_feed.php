<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/moment.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
     $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
     $conditions[] = new EqualityCondition(InternshipOrganizerMoment::PROPERTY_AGREEMENT_ID, $agreement_id);

     
    if (isset($_GET['query']))
    {
        $query = $_GET['query'];
        $search_conditions = array();
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerMoment :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, '*' . $query . '*', InternshipOrganizerMoment :: get_table_name());
        
        $conditions[] = new OrCondition($search_conditions);
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $a = array();
            $ids = explode( '|', $id);
            $a[] = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_ID, $ids[0]);
            $a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $ids[1],InternshipOrganizerAgreementRelUser :: get_table_name());
            $a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $ids[2], InternshipOrganizerAgreementRelUser :: get_table_name());
            $c[] = new AndCondition($a);
        
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
    
    $objects = InternshipOrganizerDataManager :: get_instance()->retrieve_moment_rel_users($condition);
    
    $moment_rel_users = array();
    while ($moment_rel_user = $objects->next_result())
    {
    	$moment_rel_users[] = $moment_rel_user;
    }
	
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($moment_rel_users);

echo '</tree>';

function dump_tree($moment_rel_users)
{
    if (contains_results($moment_rel_users))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('InternshipOrganizerMoments'), '">', "\n";
        
        foreach ($moment_rel_users as $moment_rel_user)
        {
           	$user_type_index = $moment_rel_user->get_optional_property(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE);
           	$user_type = InternshipOrganizerUserType :: get_user_type_name($user_type_index);
            $user_id = $moment_rel_user->get_optional_property('user_id');
        	$moment_id = $moment_rel_user->get_id();
        	$moment = InternshipOrganizerDataManager::get_instance()->retrieve_moment($moment_id);
        	$id = $moment_id . '|' . $user_id . '|' .$user_type_index ;
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            $name = strip_tags($moment->get_name() . ' ' . $user->get_firstname() . ' ' . $user->get_lastname() . ' - ' . $user_type);
            $begin = $moment->get_begin();
            $end = $moment->get_end();
	        $description =$begin.' - '.$end;
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