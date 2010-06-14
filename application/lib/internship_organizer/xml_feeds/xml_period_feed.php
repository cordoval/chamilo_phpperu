<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/period.class.php';
require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $dm = InternshipOrganizerDataManager :: get_instance();
    
//    if (isset($_GET[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID]))
//    {
//        
//        $period = $dm->retrieve_period($_GET[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID]);
//    
//    }
    
    if (isset($_GET['query']))
    {
        $q = '*' . $_GET['query'] . '*';
        $query_conditions = array();
        $query_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, $q);
        $query_conditions[] = new PatternMatchCondition(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION, $q);
        $and_condition = new AndCondition($query_conditions);
        
        if (isset($and_condition))
        {
            $conditions[] = $and_condition;
        }
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }
    
    if (isset($_GET['query']) || is_array($_GET['exclude']))
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }
    
    $objects = $dm->retrieve_periods($condition);
    
    while ($period = $objects->next_result())
    {
        $periods[] = $period;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($periods);

echo '</tree>';

function dump_tree($periods)
{
    if (contains_results($periods))
    {
        echo '<node classes="type_category unlinked" id="categories" title="' . Translation :: get('Periods') . '">';
        foreach ($periods as $period)
        {
            echo '<leaf id="' . $period->get_id() . '" classes="' . 'type type_internship_organiser_period' . '" title="' . htmlspecialchars($period->get_name()) . '" description="' . htmlspecialchars($period->get_name()) . '"/>' . "\n";
        }
        echo '</node>';
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