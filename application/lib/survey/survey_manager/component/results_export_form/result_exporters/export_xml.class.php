<?php
/**
 * $Id: export_xml.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.results_export_form.result_exporters
 */
require_once dirname(__FILE__) . '/../../../../trackers/survey_question_attempts_tracker.class.php';

class ResultsXmlExport extends ResultsExport
{

    function export_publication_id($id)
    {
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
        $survey = $publication->get_publication_object();
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_SURVEY_ID, $id);
        $track = new SurveySurveyAttemptsTracker();
        $user_surveys = $track->retrieve_tracker_items($condition);
        foreach ($user_surveys as $user_survey)
        {
            $user_data['user_survey'] = $this->export_user_survey($user_survey, $survey->get_id());
        }
        $survey_data = $this->export_survey($survey);
        $data['survey_results'] = array('survey' => $survey_data, 'results' => $user_data);
        return $data;
    }

    function export_user_survey_id($id)
    {
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_ID, $id);
        $track = new SurveySurveyAttemptsTracker();
        $user_surveys = $track->retrieve_tracker_items($condition);
        $user_survey = $user_surveys[0];
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($user_survey->get_survey_id());
        $survey = $publication->get_publication_object();
        $survey_data = $this->export_survey($survey);
        $user_data['user_survey'] = $this->export_user_survey($user_survey, $survey->get_id());
        $data['survey_results'] = array('survey' => $survey_data, 'results' => $user_data);
        return $data;
    }

    function export_survey($survey)
    {
        $data['id'] = $survey->get_id();
        $data['title'] = htmlspecialchars($survey->get_title());
        $data['description'] = htmlspecialchars($survey->get_description());
        $data['survey_type'] = htmlspecialchars($survey->get_survey_type());
        return $data;
    }

    function export_user_survey($user_survey, $survey_id)
    {
        $data['id'] = $user_survey->get_id();
        $data['survey'] = $user_survey->get_survey_id();
        $data['user'] = $this->export_user($user_survey->get_user_id());
        $data['total_score'] = $user_survey->get_total_score();
        $data['date_time_taken'] = $user_survey->get_date();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_id, ComplexContentObjectItem :: get_table_name());
        
        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $question_data[] = $this->export_question($clo_question, $user_survey);
        }
        $data['questions'] = $question_data;
        return $data;
    }

    function export_user($userid)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($userid);
        
        $data['id'] = $userid;
        $data['fullname'] = htmlspecialchars($user->get_fullname());
        return $data;
    }

    function export_question($clo_question, $user_survey)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());
        $data['id'] = $question->get_id();
        $data['title'] = $question->get_title();
        $data['description'] = htmlspecialchars($question->get_description());
        $data['type'] = htmlspecialchars($question->get_type());
        $data['weight'] = $clo_question->get_weight();
        
        $track = new SurveyQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
        $condition_a = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_SURVEY_ATTEMPT_ID, $user_survey->get_id());
        $condition = new AndCondition(array($condition_q, $condition_a));
        $user_answers = $track->retrieve_tracker_items($condition);
        $user_answer = $user_answers[0];
        
        if ($user_answer->get_feedback() != null && $user_answer->get_feedback() > 0)
            $data['feedback'] = $this->export_feedback($user_answer->get_feedback());
        
        $answers = unserialize($user_answer->get_answer());
        foreach ($answers as $answer)
        {
            if ($data['type'] == 'hotspot_question')
            {
                $coordinates = unserialize($answer);
                $answer_data['x'] = $coordinates[0];
                $answer_data['y'] = $coordinates[1];
                $data['answers'][] = $answer_data;
            }
            else
            {
                $data['answers'][] = htmlspecialchars($answer);
            }
        }
        
        $data['feedback'] = htmlspecialchars($user_answer->get_feedback());
        $data['score'] = $user_answer->get_score();
        return $data;
    }

    function export_feedback($feedback_id)
    {
        $feedback = $this->rdm->retrieve_content_object($feedback_id, 'feedback');
        $data['id'] = $feedback->get_id();
        $data['title'] = htmlspecialchars($feedback->get_title());
        $data['description'] = htmlspecialchars($feedback->get_description());
        return $data;
    }
}
?>