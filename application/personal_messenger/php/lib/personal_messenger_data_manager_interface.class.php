<?php
interface PersonalMessengerDataManagerInterface
{
    /**
     * Initializes the data manager.
     */
    function initialize();

    /**
     * Determines whether any of the given learning objects has been published
     * in this application.
     * @param array $object_ids The Id's of the learning objects
     * @return boolean True if at least one of the given objects is published in
     * this application, false otherwise
     */
    function any_content_object_is_published($object_ids);

    /**
     * Returns whether a given object id is published in this application
     * @param int $object_id
     * @return boolean Is the object is published
     */
    function content_object_is_published($object_id);

    /**
     * Gets the publication attributes of a given learning object id
     * @param int $object_id The object id
     * @return ContentObjectPublicationAttribute
     */
    function get_content_object_publication_attribute($object_id);

    /**
     * Gets the publication attributes of a given array of learning object id's
     * @param array $object_id The array of object ids
     * @return array An array of Learing Object Publication Attributes
     */
    function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return int
     */
    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    /**
     * Count the publications
     * @param Condition $condition
     * @return int
     */
    function count_personal_message_publications($condition = null);

    /**
     * Count the unread publications
     * @return int
     */
    function count_unread_personal_message_publications($user);

    /**
     * Retrieve a personal message publication
     * @param int $id
     * @return PersonalMessagePublication
     */
    function retrieve_personal_message_publication($id);

    /**
     * Retrieve a series of personal message publications
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return PersonalMessagePublicationResultSet
     */
    function retrieve_personal_message_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    /**
     * Update the publication
     * @param PersonalMessagePublication $personal_message_publication
     * @return boolean
     */
    function update_personal_message_publication($personal_message_publication);

    /**
     * Delete the publication
     * @param PersonalMessagePublication $personal_message_publication
     * @return boolean
     */
    function delete_personal_message_publication($personal_message_publication);

    /**
     * Delete the publications
     * @param Array $object_id An array of publication ids
     * @return boolean
     */
    function delete_personal_message_publications($object_id);

    /**
     * Update the publication id
     * @param ContentObjectPublicationAttribure $publication_attr
     * @return boolean
     */
    function update_personal_message_publication_id($publication_attr);

    /**
     * Create a publication
     * @param PersonalMessagePublication $publication
     * @return boolean
     */
    function create_personal_message_publication($publication);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

}
?>