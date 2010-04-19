<?php
/**
 * $Id: export_pdf.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_export_form.results_exporters
 */
class ResultsPdfExport extends ResultsExport
{
    private $data;
    
    const PROPERTY_ASSESSMENT_TITLE = 'Title';
    const PROPERTY_ASSESSMENT_DESCRIPTION = 'Description';
    const PROPERTY_ASSESSMENT_TYPE = 'Type';
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
        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($id);
        $assessment = $publication->get_content_object();
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $id);
        $user_assessments = $track->retrieve_tracker_items($condition);
        $this->export_header($assessment);
        foreach ($user_assessments as $user_assessment)
        {
            $this->export_user_assessment($user_assessment, $assessment->get_id());
        }
        return $this->data;
    }

    function export_user_assessment_id($id)
    {
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $id);
        $user_assessments = $track->retrieve_tracker_items($condition);
        $user_assessment = $user_assessments[0];
        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($user_assessment->get_assessment_id());
        $assessment = $publication->get_content_object();
        $this->export_header($assessment);
        $this->export_user_assessment($user_assessment, $assessment->get_id());
        return $this->data;
    }

    function export_header($assessment)
    {
        $data = $this->export_assessment($assessment);
        $this->data[] = array('key' => 'Assessment', 'data' => array($data));
    }

    function export_assessment($assessment)
    {
        $data[self :: PROPERTY_ASSESSMENT_TITLE] = $assessment->get_title();
        $data[self :: PROPERTY_ASSESSMENT_DESCRIPTION] = strip_tags($assessment->get_description());
        $data[self :: PROPERTY_ASSESSMENT_TYPE] = $assessment->get_assessment_type();
        return $data;
    }

    function export_user_assessment($user_assessment, $assessment_id)
    {
        $data = $this->export_user($user_assessment->get_user_id());
        $data[self :: PROPERTY_RESULT] = $user_assessment->get_total_score();
        $data[self :: PROPERTY_DATE_TIME_TAKEN] = $user_assessment->get_date();
        $this->data[] = array('key' => 'Result', 'data' => array($data));
        
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment_id, ComplexContentObjectItem :: get_table_name());
        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $this->export_question($clo_question, $user_assessment);
        }
    }

    function export_user($userid)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($userid);
        $data[self :: PROPERTY_USERNAME] = $user->get_fullname();
        return $data;
    }

    function export_question($clo_question, $user_assessment)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());
        
        $track = new WeblcmsQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
        $condition_a = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
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
            if ($question->get_type() == 'hotspot_question')
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