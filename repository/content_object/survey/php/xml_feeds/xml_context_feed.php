<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\NotCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use repository\RepositoryManager;
use common\libraries\Authentication;

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
//require_once Path :: get_repository_content_object_path() . '/survey/php/survey_context.class.php';


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
    
    if (isset($query) && ($query != ''))
    {
        $conditions[] = new PatternMatchCondition(SurveyContext :: PROPERTY_NAME, '*' . $query . '*', SurveyContext :: get_table_name());
    }
    
    if (is_array($exclude))
    {
        if (count($exclude) > 0)
        {
            $c = array();
            foreach ($exclude as $id)
            {
                if ($id != '')
                {
                   $c[] = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
                }
            }
            if (count($c) > 0)
            {
                $conditions[] = new NotCondition(new OrCondition($c));
            
            }
        }
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
            $name = $context->get_name();
            foreach ($props as $prop)
            {
                $prop = strip_tags($prop);
                $properties[] = $prop;
            }
            $description = implode(" | ", $properties);
            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars($description) . '"/>' . "\n";
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