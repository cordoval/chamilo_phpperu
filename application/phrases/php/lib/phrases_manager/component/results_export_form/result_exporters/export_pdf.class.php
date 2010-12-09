<?php
namespace application\phrases;

use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use common\libraries\AndCondition;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class ResultsPdfExport extends ResultsExport
{
    private $data;

    const PROPERTY_PHRASES_TITLE = 'Title';
    const PROPERTY_PHRASES_DESCRIPTION = 'Description';
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
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($id);
        $phrases = $publication->get_publication_object();
        $track = new PhrasesAdaptiveAssessmentAttemptTracker();
        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ID, $id);
        $user_phrasess = $track->retrieve_tracker_items($condition);
        $this->export_header($phrases);
        foreach ($user_phrasess as $user_phrases)
        {
            $this->export_user_phrases($user_phrases, $phrases->get_id());
        }
        return $this->data;
    }

    function export_user_phrases_id($id)
    {
        $track = new PhrasesAdaptiveAssessmentAttemptTracker();
        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ID, $id);
        $user_phrasess = $track->retrieve_tracker_items($condition);
        $user_phrases = $user_phrasess[0];
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($user_phrases->get_phrases_id());
        $phrases = $publication->get_publication_object();
        $this->export_header($phrases);
        $this->export_user_phrases($user_phrases, $phrases->get_id());
        return $this->data;
    }

    function export_header($phrases)
    {
        $data = $this->export_phrases($phrases);
        $this->data[] = array('key' => 'Phrases', 'data' => array($data));
    }

    function export_phrases($phrases)
    {
        $data[self :: PROPERTY_PHRASES_TITLE] = $phrases->get_title();
        $data[self :: PROPERTY_PHRASES_DESCRIPTION] = strip_tags($phrases->get_description());
        return $data;
    }

    function export_user_phrases($user_phrases, $phrases_id)
    {
        $data = $this->export_user($user_phrases->get_user_id());
        $data[self :: PROPERTY_RESULT] = $user_phrases->get_total_score();
        $data[self :: PROPERTY_DATE_TIME_TAKEN] = $user_phrases->get_date();
        $this->data[] = array('key' => 'Result', 'data' => array($data));

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $phrases_id, ComplexContentObjectItem :: get_table_name());
        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $this->export_question($clo_question, $user_phrases);
        }
    }

    function export_user($userid)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($userid);
        $data[self :: PROPERTY_USERNAME] = $user->get_fullname();
        return $data;
    }

    function export_question($clo_question, $user_phrases)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());

        $track = new PhrasesAdaptiveAssessmentQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_COMPLEX_QUESTION_ID, $clo_question->get_id());
        $condition_a = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID, $user_phrases->get_id());
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