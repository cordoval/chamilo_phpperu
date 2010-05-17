<?php
interface PersonalCalendarDataManagerInterface
{
    /**
     * Initializes the data manager.
     */
    function initialize();

    /**
     * Creates a storage unit in the personal calendar storage system
     * @param string $name
     * @param array $properties
     * @param array $indexes
     */
    function create_storage_unit($name, $properties, $indexes);

    /**
     * @see Application::content_object_is_published()
     */
    function content_object_is_published($object_id);

    /**
     * @see Application::any_content_object_is_published()
     */
    function any_content_object_is_published($object_ids);

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    function get_content_object_publication_attribute($publication_id);

    /**
     * @see Application::count_publication_attributes()
     */
    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    /**
     * @see Application::delete_content_object_publications()
     */
    function delete_content_object_publications($object_id);

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr);

    /**
     * Retrieve a profile publication
     * @param int $id
     * @return ProfilePublication
     */
    function retrieve_personal_calendar_publication($id);

    /**
     * Retrieve a series of profile publications
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return ProfilePublicationResultSet
     */
    function retrieve_personal_calendar_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    /**
     * Update the publication
     * @param ProfilePublication $profile_publication
     * @return boolean
     */
    function update_personal_calendar_publication($publication);

    /**
     * Delete the publication
     * @param ProfilePublication $profile_publication
     * @return boolean
     */
    function delete_personal_calendar_publication($publication);

    /**
     * Delete the publications
     * @param Array $object_id An array of publication ids
     * @return boolean
     */
    function delete_personal_calendar_publications($object_id);

    /**
     * Update the publication id
     * @param ContentObjectPublicationAttribure $publication_attr
     * @return boolean
     */
    function update_personal_calendar_publication_id($publication_attr);

    /**
     * Create a publication
     * @param PersonalMessagePublication $publication
     * @return boolean
     */
    function create_personal_calendar_publication($publication);

    function retrieve_personal_calendar_publication_target_groups($calendar_event_publication);

    function retrieve_personal_calendar_publication_target_users($calendar_event_publication);

}
?>