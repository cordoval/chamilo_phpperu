<?php
namespace repository\content_object\learning_path;

/**
 * A class implements the <code>LearningPathComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a WikiComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface LearningPathComplexDisplaySupport
{

    /**
     * Retrieve the learning path tracker
     * 
     * @return LearningPathTracker
     */
    function retrieve_learning_path_tracker();

    
    /**
     * Retrieve all learning path item attempt trackers for
     * a specific learning path tracker
     * 
     * @param LearningPathTracker $learning_path_tracker
     * @return array
     */
    function retrieve_tracker_items($learning_path_tracker);

    /**
     * Render the url for the learning path tree menu
     * 
     * @return string;
     */
    function get_learning_path_tree_menu_url();

    /**
     * Render the previous url
     * 
     * @param int $total_steps
     * @return string;
     */
    function get_learning_path_previous_url($total_steps);

    /**
     * Creates a learning path item tracker
     * 
     * @param LearningPathAttemptTracker $learning_path_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array LearningPathItemAttemptTracker
     */
    function create_learning_path_item_tracker($learning_path_tracker, $current_complex_content_object_item);
    
    /**
     * Get the url of the template that shows progress details
     * 
     * @param int $complex_content_object_id
     */
    function get_learning_path_content_object_item_details_url($complex_content_object_id);
    
    /**
     * Get the url of the assessment result
     * 
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details);
    
    /**
     * Get the name of the template that shows progress details
     * 
     * @return string
     */
    function get_learning_path_attempt_progress_details_reporting_template_name();
    
    /**
     * Get the name of the template that shows overall progress
     * 
     * @return string
     */
    function get_learning_path_attempt_progress_reporting_template_name();
    
    /**
     * Get the name of the application rendering the template
     * 
     * @return string
     */
    function get_learning_path_template_application_name();
    
    /**
     * Get the url for the non-showable documents
     *  
     * @param int $document_id
     */
    function get_learning_path_document_preview_url($document_id);
}
?>