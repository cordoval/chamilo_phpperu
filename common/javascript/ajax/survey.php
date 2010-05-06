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
    $question_identifier = explode('_', $question_results[0]);
    $page_index = end($question_identifier);

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

    $question_visibility = array();
    foreach($question_selections as $question_id => $question_result)
    {
        $question_visibility[$question_id] = false;
    }

    echo json_encode($question_visibility);
}
else
{
    echo json_encode(array());
}
?>