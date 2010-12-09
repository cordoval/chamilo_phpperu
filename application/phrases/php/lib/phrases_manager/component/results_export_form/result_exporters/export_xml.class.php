<?php
namespace application\phrases;

use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use common\libraries\AndCondition;
/**
 * $Id: export_xml.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.results_export_form.result_exporters
 */
require_once dirname(__FILE__) . '/../../../../../trackers/phrases_question_attempts_tracker.class.php';

class ResultsXmlExport extends ResultsExport
{

    function export_publication_id($id)
    {
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($id);
        $phrases = $publication->get_publication_object();
        $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_PHRASES_ID, $id);
        $track = new PhrasesPhrasesAttemptsTracker();
        $user_phrasess = $track->retrieve_tracker_items($condition);
        foreach ($user_phrasess as $user_phrases)
        {
            $user_data['user_phrases'] = $this->export_user_phrases($user_phrases, $phrases->get_id());
        }
        $phrases_data = $this->export_phrases($phrases);
        $data['phrases_results'] = array('phrases' => $phrases_data,
                'results' => $user_data);
        return $data;
    }

    function export_user_phrases_id($id)
    {
        $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_ID, $id);
        $track = new PhrasesPhrasesAttemptsTracker();
        $user_phrasess = $track->retrieve_tracker_items($condition);
        $user_phrases = $user_phrasess[0];
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($user_phrases->get_phrases_id());
        $phrases = $publication->get_publication_object();
        $phrases_data = $this->export_phrases($phrases);
        $user_data['user_phrases'] = $this->export_user_phrases($user_phrases, $phrases->get_id());
        $data['phrases_results'] = array('phrases' => $phrases_data,
                'results' => $user_data);
        return $data;
    }

    function export_phrases($phrases)
    {
        $data['id'] = $phrases->get_id();
        $data['title'] = htmlspecialchars($phrases->get_title());
        $data['description'] = htmlspecialchars($phrases->get_description());
        $data['phrases_type'] = htmlspecialchars($phrases->get_phrases_type());
        return $data;
    }

    function export_user_phrases($user_phrases, $phrases_id)
    {
        $data['id'] = $user_phrases->get_id();
        $data['phrases'] = $user_phrases->get_phrases_id();
        $data['user'] = $this->export_user($user_phrases->get_user_id());
        $data['total_score'] = $user_phrases->get_total_score();
        $data['date_time_taken'] = $user_phrases->get_date();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $phrases_id, ComplexContentObjectItem :: get_table_name());

        $clo_questions = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($clo_question = $clo_questions->next_result())
        {
            $question_data[] = $this->export_question($clo_question, $user_phrases);
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

    function export_question($clo_question, $user_phrases)
    {
        $question = $this->rdm->retrieve_content_object($clo_question->get_ref());
        $data['id'] = $question->get_id();
        $data['title'] = $question->get_title();
        $data['description'] = htmlspecialchars($question->get_description());
        $data['type'] = htmlspecialchars($question->get_type());
        $data['weight'] = $clo_question->get_weight();

        $track = new PhrasesQuestionAttemptsTracker();
        $condition_q = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
        $condition_a = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_PHRASES_ATTEMPT_ID, $user_phrases->get_id());
        $condition = new AndCondition(array($condition_q, $condition_a));
        $user_answers = $track->retrieve_tracker_items($condition);
        $user_answer = $user_answers[0];

        if ($user_answer->get_feedback() != null && $user_answer->get_feedback() > 0)
            $data['feedback'] = $this->export_feedback($user_answer->get_feedback());

        $answers = unserialize($user_answer->get_answer());
        foreach ($answers as $answer)
        {
            if ($data['type'] == HotspotQuestion :: get_type_name())
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
        $feedback = $this->rdm->retrieve_content_object($feedback_id, Feedback :: get_type_name());
        $data['id'] = $feedback->get_id();
        $data['title'] = htmlspecialchars($feedback->get_title());
        $data['description'] = htmlspecialchars($feedback->get_description());
        return $data;
    }
}
?>