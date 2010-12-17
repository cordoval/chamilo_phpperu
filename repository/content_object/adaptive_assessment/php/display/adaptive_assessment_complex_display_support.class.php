<?php
namespace repository\content_object\adaptive_assessment;

use repository\ComplexDisplaySupport;

/**
 * Implement this interface to support the displaying and
 * execution of adaptive assessments
 *
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

interface AdaptiveAssessmentComplexDisplaySupport extends ComplexDisplaySupport
{

    /**
     * Retrieve the learning path tracker
     *
     * @return AdaptiveAssessmentTracker
     */
    function retrieve_adaptive_assessment_tracker();

    /**
     * Retrieve all learning path item attempt trackers for
     * a specific learning path tracker
     *
     * @param AdaptiveAssessmentTracker $adaptive_assessment_tracker
     * @return array
     */
    function retrieve_adaptive_assessment_tracker_items($adaptive_assessment_tracker);

    /**
     * Render the url for the learning path tree menu
     *
     * @return string;
     */
    function get_adaptive_assessment_tree_menu_url();

    /**
     * Render the previous url
     *
     * @param int $total_steps
     * @return string;
     */
    function get_adaptive_assessment_previous_url($total_steps);

    /**
     * Creates a learning path item tracker
     *
     * @param AdaptiveAssessmentAttemptTracker $adaptive_assessment_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array AdaptiveAssessmentItemAttemptTracker
     */
    function create_adaptive_assessment_item_tracker($adaptive_assessment_tracker, $current_complex_content_object_item);

    /**
     * Get the url of the template that shows progress details
     *
     * @param int $complex_content_object_id
     */
    function get_adaptive_assessment_content_object_item_details_url($complex_content_object_id);

    /**
     * Get the url of the assessment result
     *
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_id, $details);

    /**
     * Get the name of the template that shows progress details
     *
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_details_reporting_template_name();

    /**
     * Get the name of the template that shows overall progress
     *
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_reporting_template_name();

    /**
     * Get the name of the application rendering the template
     *
     * @return string
     */
    function get_adaptive_assessment_template_application_name();
}
?>