<?php
require_once dirname(__FILE__) . '/../../global.inc.php';
require_once Path :: get_application_path() . 'lib/survey/survey_data_manager.class.php';

function process_question_results($question_results)
{
    $question_selections = array();
    
    foreach ($question_results as $question_identifier => $question_value)
    {
        $question_identifier = explode('_', $question_identifier);
        $question_type = $question_identifier[0];
        $question_id = $question_identifier[1];
        $question_name = 'survey_question_' . $question_id;
        
        $contains_matches = (count(array_slice($question_identifier, 2, - 1)) > 1);
        
        if ($question_type == 'radio')
        {
            if ($contains_matches)
            {
                $question_selections[$question_name][$question_identifier[2]] = $question_value;
            }
            else
            {
                $question_selections[$question_name] = $question_value;
            }
        }
        elseif ($question_type == 'checkbox')
        {
            if ($contains_matches)
            {
                $question_selections[$question_name][$question_identifier[2]][] = $question_identifier[3];
            }
            else
            {
                $question_selections[$question_name][] = $question_identifier[2];
            }
        }
    }
    
    return $question_selections;
}

$survey_publication_id = Request :: post('survey_publication');
$survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);

$question_results = Request :: post('results');
$question_results = str_replace('\"', '"', $question_results);
$question_results = json_decode($question_results, true);

if (count($question_results) > 0)
{
    $result;
    foreach ($question_results as $key => $result)
    {
        $result = $key;
    }
    $question_identifier = explode('_', $result);
    
//    if ($question_identifier[0] == 'radio')
//    {
//        if(count($question_identifier)== 5){
//        	$page_index = $question_identifier[4];
//        }else{
//        	$page_index = $question_identifier[1];
//        }
//    
//    }
//    else
//    {
        $page_index = array_pop($question_identifier);
//    }
    
    $question_selections = process_question_results($question_results);
    
    /**
     * Verification of question visiblity goes here.
     *
     * Expected format for $question_visibility:
     * A single dimension array containing the question ids
     * (survey_question_x) as keys and a boolean as a value
     *
     * The example below just loops through all questions
     * with selected answers and hides them
     */
//    	dump($question_results);
//        dump($page_index);
//        exit;
    //    
    $question_visibility = array();
    
    $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($page_index);
    
    $complex_questions = $survey_page->get_questions(true);
    while ($complex_question = $complex_questions->next_result())
    {
        $question_id = $complex_question->get_ref();
        
        if ($complex_question->get_visible() == 1)
        {
            $question_visibility['survey_question_'.$question_id] = true;
        }
        else
        {
            $question_visibility['survey_question_'.$question_id] = false;
        }
    
    }
    
//    dump($question_visibility);
    
    
    //    $configs = $survey_page->get_config();
    //    
    

    foreach ($question_selections as $question_id => $question_result)
    {
        foreach ($configs as $config)
        {
            if ($config[SurveyPage :: FROM_VISIBLE_QUESTION_ID] = $question_id)
            {
                $config_answers = $config[SurveyPage :: ANSWERMATCHES];
                $diff = array_diff();
                if ($config[SurveyPage :: ANSWERMATCHES])
                {
                
                }
            }
        }
//       $question_visibility[$question_id] = false;
    }
    
//    dump($question_visibility);
//    exit;
//    
    echo json_encode($question_visibility);
}
else
{
    echo json_encode(array());
}
?>