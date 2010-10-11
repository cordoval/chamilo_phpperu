<?php
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context.class.php';

//require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_data_manager.class.php';
//require_once Path :: get_library_path() . 'utilities.class.php';
//require_once Path :: get_user_path() . 'lib/user.class.php';
//require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
//require_once Path :: get_library_path() . 'condition/not_condition.class.php';
//require_once Path :: get_library_path() . 'condition/and_condition.class.php';
//require_once Path :: get_library_path() . 'condition/or_condition.class.php';
//require_once Path :: get_application_path() . '/lib/internship_organizer/period.class.php';
//require_once Path :: get_application_path() . '/lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(RepositoryManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');
	
    $type = Request :: get('context_type');
        
    $survey_context = SurveyContext :: factory($type);
    $properties = $survey_context->get_additional_property_names();
       
	$conditions = array();
    
    $query_condition = Utilities :: query_to_condition($query, $properties);
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($exclude))
    {
        $c = array();
        foreach ($exclude as $id)
        {
            $c[] = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
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
       
    
    $dm = SurveyContextDataManager :: get_instance();
    $objects = $dm->retrieve_survey_contexts($type, $condition);
    
    $contexts = array();
    
    while ($context = $objects->next_result())
    {
        $contexts[] = $context;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($contexts);

echo '</tree>';

function dump_tree($contexts)
{
    if (contains_results($contexts))
    {
        echo '<node id="0" classes="category unlinked" title="' . Translation :: get('SurveyContexts') . '">' . "\n";
        
        foreach ($contexts as $context)
        {
            $id = 'context_' . $context->get_id();
            $props = $context->get_additional_properties();
            
            $properties = array();
            foreach ($props as $prop) {
            	$prop = strip_tags($prop);
            	$properties[] = $prop;
            }
            $name = implode(" | ", $properties);
                        
            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars($name) . '"/>' . "\n";
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