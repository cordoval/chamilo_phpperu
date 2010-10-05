<?php
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . '/lib/survey/survey_data_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_application_path() . '/lib/survey/survey_manager/survey_manager.class.php';

Translation :: set_application(SurveyManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
	$conditions = array();
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(SurveyParticipantTracker::PROPERTY_CONTEXT_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(SurveyParticipantTracker::PROPERTY_CONTEXT_TEMPLATE_ID, $id);
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
    
    $pubs = SurveyDataManager::get_instance();
	$survey_publications = $pubs->retrieve_survey_publications($condition);
	
	
	$objects = array();
	foreach($survey_publications as $survey)
	{
		$objects[] = $survey->get_context_template();
	}
	
	dump($objects);
	exit();
    while ($context_template = $objects->next_result())
    {
        $context_templates[] = $context_template;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($context_templates);

echo '</tree>';

function dump_tree($context_templates)
{
    if (contains_results($context_templates))
    {
        echo '<node id="0" classes="context_template unlinked" title="', Translation :: get('ContextTemplates'), '">', "\n";
        
        foreach ($context_templates as $context_template)
        {
            $id = $context_template->get_id();
            $name = $context_templates->get_name();
            $description = $context_templates->get_description();
            
            echo '<leaf id="', $id, '" classes="', '', '" title="', htmlentities($name), '" description="', htmlentities(isset($description) && ! empty($description) ? $description : $name), '"/>', "\n";
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