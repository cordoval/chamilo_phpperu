<?php
interface GradebookDataManagerInterface
{

    /**
     * Initializes the data manager.
     */
    function initialize();

    //gradebook_items


    //gradebook evaluation format items
    function create_format($evaluation_format);

    function update_format($evaluation_format);

    function retrieve_all_active_evaluation_formats();

    function retrieve_evaluation_formats($condition = null, $offset = null, $count = null, $order_property = null);

    function count_evaluation_formats();

    function retrieve_evaluation_format($id);

    // internal items
    function retrieve_internal_item_by_publication($application, $publication_id);

    function create_internal_item($internal_item);

    function delete_internal_item($internal_item);

    function retrieve_internal_item($id);

    function retrieve_categories_by_application($application);
    
    // external items
    function retrieve_external_items($condition, $offset = null, $max_objects = null, $order_by = null);
    
    function retrieve_all_evaluations_on_external_publication($condition);
    
    function count_external_items($condition);

    // internal item instance
    function delete_internal_item_instance($internal_item_instance);

    function retrieve_evaluation_ids_by_internal_item_id($internal_item_id);

    function retrieve_internal_item_instance_by_evaluation($evaluation_id);

    function update_internal_item_instance($internal_item_instance);

    // evaluation
    //function move_internal_to_external($application, $publication);
    function retrieve_evaluation_ids_by_publication($application, $publication_id);

    function create_evaluation($evaluation);

    function delete_evaluation($evaluation);

    function update_evaluation($evaluation);

    function retrieve_all_evaluations_on_internal_publication($application, $publication_id, $offset = null, $count = null, $order_property = null);

    function count_all_evaluations_on_publication($publication_id);

    //
    //	function retrieve_all_evaluation_formats();
    //
    //	function retrieve_all_active_evaluation_formats();


    /*function get_next_gradebook_id();

	function delete_gradebook($gradebook);

	function update_gradebook($gradebook);

	function create_gradebook($gradebook);

	function truncate_gradebook($id);

	function count_gradebooks($conditions = null);*/

    function retrieve_evaluation($id);

    function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null);

    function create_evaluation_object_from_data($content_object_publication, $user, $date);

    //evaluation format


    function retrieve_format_id_by_format_name($format_name);

    //gradebook_items rel user

    //gradebook grade evaluation
    function create_grade_evaluation($grade_evaluation);

    function retrieve_grade_evaluation($id);

    function delete_grade_evaluation($grade_evaluation);

    function update_grade_evaluation($grade_evaluation);

    function create_grade_evaluation_object_from_data($tracker_score);

    function retrieve_internal_item_applications();

    function retrieve_calculated_internal_items();

    function retrieve_internal_items_by_application($condition, $offset = null, $count = null, $order_property = null);

    function count_internal_items_by_application($condition);
    /*
	function create_gradebook_rel_user($gradebookreluser);

	function delete_gradebook_rel_user($gradebookreluser);

	function update_gradebook_rel_user($gradebookreluser);

	function count_gradebook_rel_users($conditions = null);

	function retrieve_gradebook_rel_user($user_id, $gradebook_id);

	function retrieve_gradebook_rel_users($condition = null, $offset = null, $count = null, $order_property = null);
	*/
}
?>