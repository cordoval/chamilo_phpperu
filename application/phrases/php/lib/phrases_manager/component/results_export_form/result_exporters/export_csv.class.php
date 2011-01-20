<?php
namespace application\phrases;

use repository\content_object\feedback\Feedback;
use repository\content_object\hotspot_question\HotspotQuestion;
use repository\ComplexContentObjectItem;

use user\UserDataManager;

use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class ResultsCsvExport extends ResultsExport
{
    private $currentrow;
    private $data;

    const PROPERTY_PHRASES_TITLE = 'phrases_title';
    const PROPERTY_PHRASES_DESCRIPTION = 'phrases_description';
    const PROPERTY_USERNAME = 'username';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_DATE_TIME_TAKEN = 'date_time_taken';
    const PROPERTY_QUESTION_TITLE = 'question_title';
    const PROPERTY_QUESTION_DESCRIPTION = 'question_description';
    const PROPERTY_QUESTION_TYPE = 'question_type';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK_TITLE = 'feedback_title';
    const PROPERTY_FEEDBACK_DESCRIPTION = 'feedback_description';

    function export_publication_id($id)
    {
        $publication = PhrasesDataManager :: get_instance()->retrieve_content_object_publication($id);
        $phrases = $publication->get_publication_object();
        $track = new PhrasesAdaptiveAssessmentAttemptTracker();
        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ID, $publication->get_id());
        $user_phrasess = $track->retrieve_tracker_items($condition);
        $this->export_header($phrases);
        while ($user_phrases = $user_phrasess->next_result())
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
        $this->export_phrases($phrases);
        $this->data[] = $this->currentrow;
        $this->currentrow = array();
        $this->data[] = $this->currentrow;
        $this->currentrow = array(
                self :: PROPERTY_USERNAME,
                self :: PROPERTY_RESULT,
                self :: PROPERTY_DATE_TIME_TAKEN,
                self :: PROPERTY_QUESTION_TITLE,
                self :: PROPERTY_QUESTION_DESCRIPTION,
                self :: PROPERTY_QUESTION_TYPE,
                self :: PROPERTY_WEIGHT,  //self :: PROPERTY_FEEDBACK_TITLE,
                //self :: PROPERTY_FEEDBACK_DESCRIPTION,
                self :: PROPERTY_ANSWER,
                self :: PROPERTY_SCORE,
                'feedback');
        $this->data[] = $this->currentrow;
        $this->currentrow = array();
    }

    function export_phrases($phrases)
    {
        $this->currentrow[self :: PROPERTY_PHRASES_TITLE] = $phrases->get_title();
        $this->currentrow[self :: PROPERTY_PHRASES_DESCRIPTION] = strip_tags($phrases->get_description());
    }

    function empty_phrases_columns()
    {
        $this->currentrow[self :: PROPERTY_PHRASES_TITLE] = ' ';
        $this->currentrow[self :: PROPERTY_PHRASES_DESCRIPTION] = ' ';
    }

    function export_user_phrases($user_phrases, $phrases_id)
    {
        $this->export_user($user_phrases->get_user_id());
        $this->currentrow[self :: PROPERTY_RESULT] = $user_phrases->get_total_score();
        $this->currentrow[self :: PROPERTY_DATE_TIME_TAKEN] = $user_phrases->get_date();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $phrases_id, ComplexContentObjectItem :: get_table_name());
        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $this->export_question($clo_question, $user_phrases);
            $this->empty_phrases_columns();
        }
    }

    function export_user($userid)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($userid);
        $this->currentrow[self :: PROPERTY_USERNAME] = $user->get_fullname();
    }

    function export_question($clo_question, $user_phrases)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());
        $this->currentrow[self :: PROPERTY_QUESTION_TITLE] = $question->get_title();

        $description = trim(htmlspecialchars(strip_tags($question->get_description())));

        $this->currentrow[self :: PROPERTY_QUESTION_DESCRIPTION] = $description;
        $this->currentrow[self :: PROPERTY_QUESTION_TYPE] = $question->get_type();
        $this->currentrow[self :: PROPERTY_WEIGHT] = $clo_question->get_weight();

        $track = new PhrasesAdaptiveAssessmentQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_COMPLEX_QUESTION_ID, $clo_question->get_id());
        $condition_a = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID, $user_phrases->get_id());
        $condition = new AndCondition(array($condition_q, $condition_a));
        $user_answers = $track->retrieve_tracker_items($condition);
        $user_answer = $user_answers[0];

        if ($user_answer->get_feedback() != null && $user_answer->get_feedback() > 0)
            $data['feedback'] = $this->export_feedback($user_answer->get_feedback());

        $answers = unserialize($user_answer->get_answer());
        $answer_data = array();
        foreach ($answers as $answer)
        {
            if ($question->get_type() == HotspotQuestion :: get_type_name())
            {
                $coordinates = unserialize($answer);
                $data['x'] = $coordinates[0];
                $data['y'] = $coordinates[1];
                $answer_data[] = implode(", ", $data);
            }
            else
            {
                $answer_data[] = htmlspecialchars(strip_tags($answer));
            }
        }

        $this->currentrow[self :: PROPERTY_ANSWER] = implode(" / ", $answer_data);
        $this->currentrow[self :: PROPERTY_SCORE] = $user_answer->get_score();
        $this->currentrow['feedback'] = htmlspecialchars($user_answer->get_feedback());
        $this->data[] = $this->currentrow;
    }

    function export_feedback($feedback_id)
    {
        $feedback = $this->rdm->retrieve_content_object($feedback_id, Feedback :: get_type_name());
        $this->currentrow[self :: PROPERTY_FEEDBACK_TITLE] = $feedback->get_title();
        $this->currentrow[self :: PROPERTY_FEEDBACK_DESCRIPTION] = strip_tags($feedback->get_description());
    }

}
?>