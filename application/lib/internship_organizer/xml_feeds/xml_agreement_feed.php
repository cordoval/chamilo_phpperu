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
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME, User :: PROPERTY_USERNAME));
    if (isset($query_condition))
    {
        $search_conditions = array();
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerAgreement :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, '*' . $query . '*', InternshipOrganizerAgreement :: get_table_name());
        
        $conditions[] = new OrCondition($search_conditions);
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $a = array();
        	$ids = array_explode($id, '|');
        	$a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_AGREEMENT_ID, $ids[0]);
           	$a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $ids[1]);
           	$a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $ids[2]);
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
    
    $objects = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_users($condition);
    
    $agreement_rel_users = array();
    while ($agreement_rel_user = $objects->next_result())
    {
        $agreement_rel_users[] = $agreement_rel_user;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($agreement_rel_users);

echo '</tree>';

function dump_tree($agreement_rel_users)
{
    if (contains_results($agreement_rel_users))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Users'), '">', "\n";
        
        foreach ($agreement_rel_users as $agreement_rel_user)
        {
            $id = $agreement_rel_user->get_agreement_id().'|'.$agreement_rel_user->get_user_id().'|'.$agreement_rel_user->get_user_type();
            $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_rel_user->get_agreement_id());
            $user = UserDataManager :: get_instance()->retrieve_user($agreement_rel_user->get_user_id());
            $name = strip_tags($agreement->get_name() . ' ' . $user->get_firstname().' '.$user->get_lastname());
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