<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
require_once Path :: get_common_libraries_path() . 'utilities.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'category.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(InternshipOrganizerCategory :: PROPERTY_NAME, InternshipOrganizerCategory :: PROPERTY_DESCRIPTION));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, $id);
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
    $objects = $dm->retrieve_categories($condition);
    
    while ($category = $objects->next_result())
    {
        $categories[] = $category;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($categories);

echo '</tree>';

function dump_tree($categories)
{
    if (contains_results($categories))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Categories'), '">', "\n";
        
        foreach ($categories as $category)
        {
            $id = 'category_' . $category->get_id();
            $name = $category->get_name();
            $description = $category->get_description();
            
            echo '<leaf id="' . $id . '" classes="" title="' . htmlentities($name) . '" description="' . htmlentities(isset($description) && ! empty($description) ? $description : $name), '"/>' . "\n";
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