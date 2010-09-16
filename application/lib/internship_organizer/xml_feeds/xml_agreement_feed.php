<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/agreement_rel_user.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $user_id = $_GET['user_id'];
    $agreement_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreementRelUser :: get_table_name());
    $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $user_id, $agreement_rel_user_alias, true);
    //    $query_condition = Utilities :: query_to_condition($_GET['query'], array(User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME, User :: PROPERTY_USERNAME));
    if (isset($_GET['query']))
    {
        $query = $_GET['query'];
        $search_conditions = array();
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', $user_alias, true);
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_NAME, '*' . $query . '*', InternshipOrganizerAgreement :: get_table_name());
        $search_conditions[] = new PatternMatchCondition(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, '*' . $query . '*', InternshipOrganizerAgreement :: get_table_name());
        
        $conditions[] = new OrCondition($search_conditions);
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            //            $a = array();
            //            $ids = explode( '_', $id);
            //            dump($id);
            //            exit;
            //            $a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_AGREEMENT_ID, $ids[0]);
            //            $a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $ids[1]);
            //            $a[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $ids[2]);
            //            $c[] = new AndCondition($a);
            $c[] = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $id);
        
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
    
    $objects = InternshipOrganizerDataManager :: get_instance()->retrieve_agreements($condition);
    
    $agreements = array();
    while ($agreement = $objects->next_result())
    {
        
        $agreements[] = $agreement;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($agreements);

echo '</tree>';

function dump_tree($agreements)
{
    if (contains_results($agreements))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('InternshipOrganizerAgreements'), '">', "\n";
        
        foreach ($agreements as $agreement)
        {
            $id = 'agreement_' . $agreement->get_id();
            $name = strip_tags($agreement->get_name() . ' ' . $agreement->get_optional_property(User :: PROPERTY_FIRSTNAME) . ' ' . $agreement->get_optional_property(User :: PROPERTY_LASTNAME));
            $description = strip_tags($agreement->get_description());
            $description = preg_replace("/[\n\r]/", "", $description);
            $description = $description . ' - ' . $agreement->get_optional_property('period');
            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $name) . '"/>' . "\n";
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