<?php
namespace repository\content_object\assessment;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

class AssessmentComplexDisplayPreview extends ComplexDisplayPreview implements AssessmentComplexDisplaySupport
{

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
    }

    /**
     * Preview mode, so no actual total score will be saved.
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score)
    {
    }

    /**
     * Preview mode, so there is no acrual attempt.
     */
    function get_assessment_current_attempt_id()
    {
    }

    /**
     * Preview mode is launched in standalone mode,
     * so there's nothing to go back to.
     *
     * @return void
     */
    function get_assessment_go_back_url()
    {
    }
}
?>