<?php
namespace application\survey;

use common\libraries\Translation;
use common\libraries\Authentication;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;

use common\libraries\AndCondition;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

Translation :: set_application(SurveyManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();
    
    $publication_id = $_GET[SurveyManager :: PARAM_PUBLICATION_ID];
    $context_template_id = $_GET[SurveyReportingManager :: PARAM_CONTEXT_TEMPLATE_ID];
    
    $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
    $survey = $pub->get_publication_object();
    
    if ($context_template_id)
    {
        $complex_questions = $survey->get_complex_questions_for_context_template_ids(array($context_template_id));
    
    }
    else
    {
        $complex_questions = $survey->get_complex_questions();
    }
    
    $query = $_GET['query'];
    if ($query)
    {
//          $conditions[] = new PatternMatchCondition(ContentObject:: PROPERTY_TITLE, '*'.$query.'*', ContentObject :: get_table_name());
    }
    
    $c = array();
    if (is_array($_GET['exclude']))
    {
        foreach ($_GET['exclude'] as $id)
        {
			$c[] = $id;
//        	$c[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $id);
        }
//        $conditions[] = new NotCondition(new OrCondition($c));
    }
    
    if (count($conditions) > 0)
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }
    
    foreach ($complex_questions as $complex_question_id => $complex_question)
    {
        
        $question = $complex_question->get_ref_object();
        if (! $question instanceof SurveyDescription)
        {
            if(!in_array($complex_question_id, $c)){
            	$questions[$complex_question_id] = $question->get_title();
            }
        }
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($questions);

echo '</tree>';

function dump_tree($questions)
{
    if (contains_results($questions))
    {
        echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Questions') . '">' . "\n";
        
        foreach ($questions as $complex_question_id => $question_title)
        {
            $id = 'question_' . $complex_question_id;
            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($question_title) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $question_title) . '"/>' . "\n";
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