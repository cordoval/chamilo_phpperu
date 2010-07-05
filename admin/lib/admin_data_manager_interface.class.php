<?php
/**
 * @package admin.lib
 *
 * This is an interface for a data manager for the Home application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface AdminDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_language($language);

    function create_registration($registration);

    function create_setting($setting);

    function create_system_announcement_publication($system_announcement_publication);

    function retrieve_languages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function count_settings($condition = null);

    function retrieve_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function retrieve_registration($id);

    function retrieve_remote_package($id);

    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function count_remote_packages($condition = null);

    function count_registrations($condition = null);

    function retrieve_setting_from_variable_name($variable, $application = 'admin');

    function retrieve_language_from_english_name($english_name);

    function retrieve_language($id);

    function retrieve_feedback_publications($pid, $cid, $application);

    function retrieve_feedback_publication($id);

    function retrieve_validation($id);

    function update_setting($setting);

    function update_registration($registration);

    function update_system_announcement_publication($system_announcement_publication);

    function delete_registration($registration);

    function delete_setting($setting);

    function delete_system_announcement_publication($system_announcement_publication);

    /**
     * Count the system announcements
     * @param Condition $condition
     * @return int
     */
    function count_system_announcement_publications($condition = null);

    /**
     * Retrieve a system announcement
     * @param int $id
     * @return SystemAnnouncementPublication
     */
    function retrieve_system_announcement_publication($id);

    /**
     * Retrieve a series of system announcements
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return SystemAnnouncementPublicationResultSet
     */
    function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function retrieve_system_announcement_publication_target_groups($system_announcement_publication);

    function retrieve_system_announcement_publication_target_users($system_announcement_publication);

    function select_next_display_order($parent_category_id);

    function delete_category($category);

    function update_category($category);

    function create_category($category);

    function count_categories($conditions = null);

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    function get_content_object_publication_attribute($publication_id);

    function any_content_object_is_published($object_ids);

    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    function delete_content_object_publications($object_id);

    function delete_settings($condition = null);

    function delete_validation($validation);

    function update_validation($validation);

    function create_validation($validation);

    function delete_feedback_publication($feeback_publication);

    function update_feedback_publication($feedback_publication);

    function create_feedback_publication($feedback_publication);

    function retrieve_validations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function count_validations($condition = null);

    // Dynamic Forms
    

    function delete_dynamic_form($dynamic_form);

    function update_dynamic_form($dynamic_form);

    function create_dynamic_form($dynamic_form);

    function count_dynamic_forms($conditions = null);

    function retrieve_dynamic_forms($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_dynamic_form_element($dynamic_form_element);

    function update_dynamic_form_element($dynamic_form_element);

    function create_dynamic_form_element($dynamic_form_element);

    function count_dynamic_form_elements($conditions = null);

    function retrieve_dynamic_form_elements($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_dynamic_form_element_order($dynamic_form_id);

    function delete_dynamic_form_element_option($dynamic_form_element_option);

    function update_dynamic_form_element_option($dynamic_form_element_option);

    function create_dynamic_form_element_option($dynamic_form_element_option);

    function count_dynamic_form_element_options($conditions = null);

    function retrieve_dynamic_form_element_options($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_dynamic_form_element_option_order($dynamic_form_element_id);

    function delete_all_options_from_form_element($dynamic_form_element_id);

    function delete_dynamic_form_element_value($dynamic_form_element_value);

    function update_dynamic_form_element_value($dynamic_form_element_value);

    function create_dynamic_form_element_value($dynamic_form_element_value);

    function count_dynamic_form_element_values($conditions = null);

    function retrieve_dynamic_form_element_values($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_dynamic_form_element_values_from_form($dynamic_form_id);

    /**
     * @param int $id
     * @return Invitation
     */
    function retrieve_invitation($id);

    function retrieve_invitations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function create_invitation($invitation);

    function delete_invitation($invitation);

    /**
     * @param string $code
     * @return Invitation
     */
    function retrieve_invitation_by_code($code);

}
?>