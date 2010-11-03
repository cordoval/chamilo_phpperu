<?php
namespace repository\content_object\assessment;

/**
 * A class implements the <code>AssessmentComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a AssessmentComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface AssessmentComplexDisplaySupport
{

    /**
     * Save the user's answer for a question
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_answer($complex_question_id, $answer, $score);

    /**
     * Write the total score to persistent storage
     *
     * @param int $total_score
     */
    function finish_assessment($total_score);

    /**
     * Get the current assessment attempt id
     */
    function get_current_attempt_id();

    /**
     * Get the url to go back to after finishing the assessment
     *
     * @return string
     */
    function get_go_back_url();
}
?>