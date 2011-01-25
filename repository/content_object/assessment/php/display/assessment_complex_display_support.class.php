<?php
namespace repository\content_object\assessment;

use repository\ComplexDisplaySupport;

/**
 * A class implements the <code>AssessmentComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a AssessmentComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface AssessmentComplexDisplaySupport extends ComplexDisplaySupport
{

    /**
     * Save the user's answer for a question
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_assessment_answer($complex_question_id, $answer, $score);

    /**
     * Write the total score to persistent storage
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score);

    /**
     * Get the current assessment attempt id
     */
    function get_assessment_current_attempt_id();

    /**
     * Get the question attempt trackers for all question in
     * a specific assessment context
     * @return multitype<QuestionAttemptsTracker>
     */
    function get_assessment_question_attempts();

    /**
     * Get the question attempt tracker for a specific question in
     * a specific assessment context
     *
     * @param integer $complex_question_id
     * @return QuestionAttemptsTracker
     */
    function get_assessment_question_attempt($complex_question_id);

    /**
     * Get the url to go back to after finishing the assessment
     *
     * @return string
     */
    function get_assessment_back_url();

    /**
     * Get the url to continue to after finishing this assessment
     * (Particularly useful in complex structures)
     *
     * @return string
     */
    function get_assessment_continue_url();

    /**
     * Get the configuration parameters for the display of the assessment
     * @return FeedbackDisplayConfiguration
     */
    function get_assessment_feedback_configuration();
}
?>