<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/period_rel_user.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $period_id = $_GET[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID];
    $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $period_id);
    
    if (isset($_GET[InternshipOrganizerPeriodManager :: PARAM_USER_TYPE]))
    {
        $user_type = $_GET[InternshipOrganizerPeriodManager :: PARAM_USER_TYPE];
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_type);
    }
    if (isset($_GET['query']))
    {
        $query = $_GET['query'];
        $search_conditions = array();
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', $user_alias, true);
        
        $conditions[] = new OrCondition($search_conditions);
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $a = array();
            $ids = explode('|', $id);
            $a[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $ids[0]);
            $a[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, $ids[1]);
            $a[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $ids[2]);
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
    
    $dm = InternshipOrganizerDataManager :: get_instance();
    $objects = $dm->retrieve_period_rel_users($condition);
    
    while ($period_rel_user = $objects->next_result())
    {
        $period_rel_users[] = $period_rel_user;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($period_rel_users);

echo '</tree>';

function dump_tree($period_rel_users)
{
    if (contains_results($period_rel_users))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Users'), '">', "\n";
        
        foreach ($period_rel_users as $period_rel_user)
        {
            $id = 'user_' . $period_rel_user->get_user_id();
//            $id = 'user_' . $period_rel_user->get_user_id() . '|' . $period_rel_user->get_user_type();
            $user_type = InternshipOrganizerUserType :: get_user_type_name($period_rel_user->get_user_type());
            $user = UserDataManager :: get_instance()->retrieve_user($period_rel_user->get_user_id());
            $name = strip_tags($user->get_firstname() . ' ' . $user->get_lastname() . ' - ' . $user_type);
            
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