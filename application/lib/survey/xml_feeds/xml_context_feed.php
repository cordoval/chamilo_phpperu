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
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_rel_user.class.php';

Translation :: set_application(SurveyManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $user_id = $_GET[SurveyManager :: PARAM_USER_ID];
    $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_id);
      
    $context_template_id = $_GET[SurveyReportingManager :: PARAM_CONTEXT_TEMPLATE_ID];
    
    if ($context_template_id)
    {
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        $context_type = $context_template->get_context_type();
        $conditions[] = new EqualityCondition(SurveyContext :: PROPERTY_TYPE, $context_type, SurveyContext :: get_table_name());
    }
    
    $query_condition = Utilities :: query_to_condition($_GET['query'], array(SurveyContext :: PROPERTY_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }
    
    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $id);
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
    $objects = $dm->retrieve_survey_context_rel_users($condition);
    
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
        echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Contexts') . '">' . "\n";
        
        foreach ($contexts as $context)
        {
            $id = $context->get_context_id();
            $name = strip_tags($context->get_optional_property(SurveyContext :: PROPERTY_NAME));
            //            $description = strip_tags($period->get_description());
            //            $description = preg_replace("/[\n\r]/", "", $description);
            

            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $name) . '"/>' . "\n";
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