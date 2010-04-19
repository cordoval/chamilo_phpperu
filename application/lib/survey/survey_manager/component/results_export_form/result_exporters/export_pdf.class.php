<?php
/**
 * $Id: export_pdf.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.results_export_form.result_exporters
 */
class ResultsPdfExport extends ResultsExport
{
    private $data;
    
    const PROPERTY_SURVEY_TITLE = 'Title';
    const PROPERTY_SURVEY_DESCRIPTION = 'Description';
    const PROPERTY_SURVEY_TYPE = 'Type';
    const PROPERTY_USERNAME = 'Username';
    const PROPERTY_RESULT = 'Result';
    const PROPERTY_DATE_TIME_TAKEN = 'TakenOn';
    const PROPERTY_QUESTION_TITLE = 'QuestionTitle';
    const PROPERTY_QUESTION_DESCRIPTION = 'QuestionDescription';
    const PROPERTY_WEIGHT = 'Weight';
    const PROPERTY_ANSWER = 'Answer';
    const PROPERTY_SCORE = 'Score';
    const PROPERTY_FEEDBACK_TITLE = 'FeedbackTitle';
    const PROPERTY_FEEDBACK_DESCRIPTION = 'FeedbackDescription';
    const PROPERTY_QUESTION_TYPE = 'Type';

    function export_publication_id($id)
    {
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
        $survey = $publication->get_publication_object();
        $track = new SurveySurveyAttemptsTracker();
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_SURVEY_ID, $id);
        $user_surveys = $track->retrieve_tracker_items($condition);
        $this->export_header($survey);
        foreach ($user_surveys as $user_survey)
        {
            $this->export_user_survey($user_survey, $survey->get_id());
        }
        return $this->data;
    }

    function export_user_survey_id($id)
    {
        $track = new SurveySurveyAttemptsTracker();
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_ID, $id);
        $user_surveys = $track->retrieve_tracker_items($condition);
        $user_survey = $user_surveys[0];
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($user_survey->get_survey_id());
        $survey = $publication->get_publication_object();
        $this->export_header($survey);
        $this->export_user_survey($user_survey, $survey->get_id());
        return $this->data;
    }

    function export_header($survey)
    {
        $data = $this->export_survey($survey);
        $this->data[] = array('key' => 'Survey', 'data' => array($data));
    }

    function export_survey($survey)
    {
        $data[self :: PROPERTY_SURVEY_TITLE] = $survey->get_title();
        $data[self :: PROPERTY_SURVEY_DESCRIPTION] = strip_tags($survey->get_description());
        $data[self :: PROPERTY_SURVEY_TYPE] = $survey->get_survey_type();
        return $data;
    }

    function export_user_survey($user_survey, $survey_id)
    {
        $data = $this->export_user($user_survey->get_user_id());
        $data[self :: PROPERTY_RESULT] = $user_survey->get_total_score();
        $data[self :: PROPERTY_DATE_TIME_TAKEN] = $user_survey->get_date();
        $this->data[] = array('key' => 'Result', 'data' => array($data));
        
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_id, ComplexContentObjectItem :: get_table_name());
        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $this->export_question($clo_question, $user_survey);
        }
    }

    function export_user($userid)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($userid);
        $data[self :: PROPERTY_USERNAME] = $user->get_fullname();
        return $data;
    }

    function export_question($clo_question, $user_survey)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());
        
        $track = new SurveyQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
        $condition_a = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_SURVEY_ATTEMPT_ID, $user_survey->get_id());
        $condition = new AndCondition(array($condition_q, $condition_a));
        $user_answers = $track->retrieve_tracker_items($condition);
        $user_answer = $user_answers[0];
        
        $data[self :: PROPERTY_QUESTION_TITLE] = $question->get_title();
        $data[self :: PROPERTY_QUESTION_TYPE] = $question->get_type();
        $data[self :: PROPERTY_QUESTION_DESCRIPTION] = strip_tags($question->get_description());
        $data[self :: PROPERTY_WEIGHT] = strip_tags($clo_question->get_weight());
        $data['score'] = $user_answer->get_score();
        $data['feedback'] = htmlspecialchars($user_answer->get_feedback());
        $this->data[] = array('key' => 'Question', 'data' => array($data));
        
        $data = array();
        $answers = unserialize($user_answer->get_answer());
        foreach ($answers as $answer)
        {
            if ($question->get_type() == HotspotQuestion :: get_type_name())
            {
                $coordinates = unserialize($answer);
                $answer_data['x'] = $coordinates[0];
                $answer_data['y'] = $coordinates[1];
                $data[] = $answer_data;
            }
            else
            {
                $data['Answers'][] = htmlspecialchars($answer);
            }
        
        }
        
        $this->data[] = array('key' => 'Answers', 'data' => $data);
    
    }

    function export_feedback($feedback_id)
    {
        $feedback = $this->rdm->retrieve_content_object($feedback_id, Feedback :: get_type_name());
        $data[self :: PROPERTY_FEEDBACK_TITLE] = $feedback->get_title();
        $data[self :: PROPERTY_FEEDBACK_DESCRIPTION] = strip_tags($feedback->get_description());
        $this->data[] = array('key' => 'Feedback', 'data' => array($data));
    }

    function export_answer($user_answer)
    {
        $data[self :: PROPERTY_ANSWER] = $user_answer->get_answer();
        $data[self :: PROPERTY_SCORE] = $user_answer->get_score();
        return $data;
    }
}
?>