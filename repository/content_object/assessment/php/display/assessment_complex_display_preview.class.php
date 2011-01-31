<?php
namespace repository\content_object\assessment;

use common\libraries\Session;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

class AssessmentComplexDisplayPreview extends ComplexDisplayPreview implements AssessmentComplexDisplaySupport
{
    const TEMPORARY_STORAGE = 'assessment_preview';

    function run()
    {
        ComplexDisplay :: launch(Assessment :: get_type_name(), $this);
    }

    /**
     * Preview mode, so always return true.
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }

    /**
     * Preview mode, so no actual saving done.
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID] = $this->get_root_content_object()->get_id();
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answers[$this->get_root_content_object()->get_id()][$complex_question_id] = new DummyQuestionAttemptsTracker($parameters);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    /**
     * Preview mode, so no actual total score will be saved.
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        unset($answers[$this->get_root_content_object()->get_id()]);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    /**
     * Preview mode, so there is no acrual attempt.
     */
    function get_assessment_current_attempt_id()
    {
    }

    function get_assessment_question_attempts()
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        return $answers[$this->get_root_content_object()->get_id()];
    }

    function get_assessment_question_attempt($complex_question_id)
    {
        $answers = $this->get_assessment_question_attempts($complex_question_id);
        return $answers[$complex_question_id];
    }

    /**
     * Preview mode is launched in standalone mode,
     * so there's nothing to go back to.
     *
     * @return void
     */
    function get_assessment_back_url()
    {
        return null;
    }

    /**
     * Preview mode is launched in standalone mode,
     * so there's nothing to continue to.
     *
     * @return void
     */
    function get_assessment_continue_url()
    {
        return null;
    }

    function get_assessment_feedback_configuration()
    {
        $dummy_configuration = new FeedbackDisplayConfiguration();
        $dummy_configuration->set_feedback_type(FeedbackDisplayConfiguration :: TYPE_BOTH);
        $dummy_configuration->disable_feedback_per_page();
        $dummy_configuration->enable_feedback_summary();
        return $dummy_configuration;
    }

    function get_assessment_parameters()
    {
        return array();
    }
}
?>