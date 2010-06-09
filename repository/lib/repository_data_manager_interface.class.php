<?php
/**
 * @package repository.lib
 *
 * This is an interface for a data manager for the Repository application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface RepositoryDataManagerInterface
{

    /**
     * Is the learning object attached to another one ?
     * @param ContentObject The learning object.
     * @return boolean Is Attached.
     */
    function is_attached($object, $type = null);

    /**
     * Determines whether a learning object can be edited.
     * @param ContentObject $object
     * @return boolean True if the given learning object can be edited
     */
    function is_latest_version($object);

    /**
     * Gets all ids of all children/grandchildren/... of a given learning
     * object.
     * @param ContentObject $object The learning object
     * @return array The requested id's
     */
    function get_children_ids($object);

    /**
     * Get number of times a physical document is used by a learning object's versions.
     * @param String $path The document path
     * @return boolean True if the physical document occurs only once, else False.
     */
    function is_only_document_occurence($path);

    /**
     * Gets all ids of all versions of a given learning object.
     * @param ContentObject $object The learning object
     * @return array The requested id's
     */
    function get_version_ids($object);

    /**
     * Initializes the data manager.
     */
    function initialize();

    /**
     * Determines the type of the learning object with the given ID.
     * @param int $id The ID of the learning object.
     * @return string The learning object type.
     */
    function determine_content_object_type($id);

    /**
     * Retrieves the learning object with the given ID from persistent
     * storage. If the type of learning object is known, it should be
     * passed in order to save time.
     * @param int $id The ID of the learning object.
     * @param string $type The type of the learning object. May be omitted.
     * @return ContentObject The learning object.
     */
    function retrieve_content_object($id, $type = null);

    /**
     * Retrieves the learning objects that match the given criteria from
     * persistent storage.
     * As far as ordering goes, there are two things to take into account:
     * - If, after applying the passed conditions, there is no order between
     * two learning objects, the display order index should be taken into
     * account.
     * - Regardless of what the order specification states, learning objects
     * of the "category" types must always come before others.
     * Finally, there are some limitations to this method:
     * - For now, you can only use the standard learning object properties,
     * not the type-specific ones IF you do not specify a single type of
     * learning object to retrieve.
     * - Future versions may include statistical functions.
     * @param string $type The type of learning objects to retrieve, if any.
     * If you do not specify a type, or the type is not
     * known in advance, you will only be able to select
     * on default properties; also, there will be a
     * significant performance decrease. In this case,
     * the values of the additional properties will not
     * yet be known; they will be retrieved JIT, i.e.
     * right before they are accessed.
     * @param Condition $condition The condition to use for learning object
     * selection, structured as a Condition
     * object. Please consult the appropriate
     * documentation.
     * @param array $order_by An array of properties to sort the learning
     * objects on.
     * @param int $offset The index of the first object to return. If
     * omitted or negative, the result set will start
     * from the first object.
     * @param int $max_objects The maximum number of objects to return. If
     * omitted or non-positive, every object from the
     * first index will be returned.
     * @param int $state The state the learning objects should have. Any of
     * the ContentObject :: STATE_* constants. A negative
     * number means the state should be ignored. Defaults
     * to ContentObject :: STATE_NORMAL. You can just as
     * easily use your own condition for this; this
     * parameter is merely for convenience, and to ensure
     * that the function does not apply to recycled objects
     * by default.
     * @return ResultSet A set of matching learning objects.
     */
    function retrieve_content_objects($condition = null, $order_by = array (), $offset = 0, $max_objects = -1, $query = null);

    function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    /**
     * Retrieves the additional properties of the given learning object.
     * @param ContentObject $content_object The learning object for which to
     * fetch additional properties.
     * @return array The properties as an associative array.
     */
    function retrieve_additional_content_object_properties($content_object);

    /**
     * Returns the number of learning objects that match the given criteria.
     * This method has the same limitations as retrieve_content_objects.
     * @param string $type The type of learning objects to search for, if any.
     * If you do not specify a type, or the type is not
     * known in advance, you will only be able to select
     * on default properties; also, there will be a
     * significant performance decrease.
     * @param Condition $condition The condition to use for learning object
     * selection, structured as a Condition
     * object. Please consult the appropriate
     * documentation.
     * @param int $state The state the learning objects should have. Any of
     * the ContentObject :: STATE_* constants. A negative
     * number means the state should be ignored. Defaults
     * to ContentObject :: STATE_NORMAL. You can just as
     * easily use your own condition for this; this
     * parameter is merely for convenience, and to ensure
     * that the function does not apply to recycled objects
     * by default.
     * @return int The number of matching learning objects.
     */
    function count_content_objects($condition = null, $query = null);

    function count_type_content_objects($type, $condition = null);

    /**
     * Returns the next available learning object number.
     * @return int The ID.
     */
    function get_next_content_object_number();

    /**
     * Makes the given learning object persistent.
     * @param ContentObject $object The learning object.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_content_object($object, $type);

    /**
     * Updates the given learning object in persistent storage.
     * @param ContentObject $object The learning object.
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_content_object($object);

    /**
     * Deletes the given learning object from persistent storage.
     * This function deletes
     * - all children of the given learning object (using this function
     * recursively)
     * - links from this object to other objects (so called attachments)
     * - links from other objects to this object (so called attachments)
     * - the object itself
     * @param ContentObject $object The learning object.
     * @return boolean True if the given object was succesfully deleted, false
     * otherwise. Deletion fails when the object is used
     * somewhere in an application or if one of its children
     * is in use.
     */
    function delete_content_object($object);

    /**
     * Creates a new complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function create_complex_content_object_item($clo_item);

    /**
     * Updates a complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function update_complex_content_object_item($clo_item);

    /**
     * Deletes a complex learning object in the database
     * @param ComplexContentObject $clo - The complex learning object
     * @return True if success
     */
    function delete_complex_content_object_item($clo_item);

    /**
     * Retrieves a complex learning object from the database with a given id
     * @param Int $clo_id
     * @return The complex learning object
     */
    function retrieve_complex_content_object_item($clo_item_id);

    /**
     * Counts the available complex learning objects with the given condition
     * @param Condition $condition
     * @return Int the amount of complex learning objects
     */
    function count_complex_content_object_items($condition);

    /**
     * Retrieves the complex learning object items with the given condition
     * @param Condition
     */
    function retrieve_complex_content_object_items($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    /**
     * Deletes the given learning object version from persistent storage.
     * This function deletes
     * - the selected version
     * This function updates
     * - the latest version entry if necessary
     * @param ContentObject $object The learning object.
     * @return boolean True if the given version was succesfully deleted, false
     * otherwise. Deletion fails when the version is used
     * somewhere in an application or if one of its children
     * is in use.
     */
    function delete_content_object_version($object);

    /**
     * Gets all learning objects from this user id, and removes them
     */
    function retrieve_content_object_by_user($user_id);

    function delete_content_object_attachments($object);

    function delete_content_object_includes($object);

    function delete_assisting_content_objects($object);

    /**
     * Deletes all known learning objects from persistent storage.
     * @note Only for testing purpuses. This function also deletes the root
     * category of a user's repository.
     */
    function delete_all_content_objects();

    /**
     * Gets the next available index in the display order.
     * @param int $parent The numeric identifier of the learning object's
     * parent learning object.
     * @param string $type The type of learning object.
     * @return int The requested display order index.
     */
    function get_next_content_object_display_order_index($parent, $type);

    /**
     * Returns the learning objects that are attached to the learning object
     * with the given ID.
     * @param ContentObject $object The learning object for which to retrieve
     * attachments.
     * @return array The attached learning objects.
     */
    function retrieve_attached_content_objects($object);

    /**
     * Counts the content objects to which the selected content object are attached to
     *
     * @param ContentObject $object The content object
     * @return int The count
     */
    function count_objects_to_which_object_is_attached($object);

    /**
     * Returns the content objects to which the selected content object are attached to
     *
     * @param ContentObject $object The content object
     * @return array The the content objects to which the selected content object are attached to
     */
    function retrieve_objects_to_which_object_is_attached($object);

    /**
     * Returns the learning objects that are included into the learning object
     * with the given ID.
     * @param ContentObject $object The learning object for which to retrieve
     * includes.
     * @return array The included learning objects.
     */
    function retrieve_included_content_objects($object);

    /**
     * Returns the content objects in which the selected content object are included
     *
     * @param ContentObject $object The content object
     * @return array The the content objects in which the selected content object are included
     */
    function retrieve_objects_in_which_object_is_included($object);

    function is_content_object_already_included($content_object, $include_object_id);

    /**
     * Counts the content objects in which the selected content object are included
     *
     * @param ContentObject $object The content object
     * @return int The count
     */
    function count_objects_in_which_object_is_included($object);

    function retrieve_content_object_versions($object, $include_last = true);

    function get_latest_version_id($object);

    /**
     * Adds a learning object to another's attachment list.
     * @param ContentObject $object The learning object to attach the other
     * learning object to.
     * @param int $attachment_id The ID of the object to attach.
     */
    function attach_content_object($object, $attachment_id);

    /**
     * Removes a learning object from another's attachment list.
     * @param ContentObject $object The learning object to detach the other
     * learning object from.
     * @param int $attachment_id The ID of the object to detach.
     * @return boolean True if the attachment was removed, false if it did not
     * exist.
     */
    function detach_content_object($object, $attachment_id);

    /**
     * Adds a learning object to another's include list.
     * @param ContentObject $object The learning object to include into the other
     * learning object.
     * @param int $attachment_id The ID of the object to include.
     */
    function include_content_object($object, $include_id);

    /**
     * Removes a learning object from another's include list.
     * @param ContentObject $object The learning object to exclude from the other
     * learning object.
     * @param int $attachment_id The ID of the object to exclude.
     * @return boolean True if the include was removed, false if it did not
     * exist.
     */
    function exclude_content_object($object, $include_id);

    /**
     * Sets the requested learning objects' state to one of the STATE_*
     * constants defined in the ContentObject class. This function's main use
     * is to make a learning object's children inherit its state.
     * @param array $object_ids The learning object IDs.
     * @param int $state The new state.
     * @return boolean True upon success, false upon failure.
     */
    function set_content_object_states($object_ids, $state);

    /**
     * Gets the disk space consumed by the given user.
     * @param int $user The user ID.
     * @return int The number of bytes used.
     */
    function get_used_disk_space($user);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

    function select_next_category_display_order($parent_category_id, $user_id);

    function delete_category($category);

    function update_category($category);

    function create_category($category);

    function count_categories($conditions = null);

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_user_view($user_view);

    function update_user_view($user_view);

    function create_user_view($user_view);

    function count_user_views($conditions = null);

    function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null);

    function update_user_view_rel_content_object($user_view_rel_content_object);

    function create_user_view_rel_content_object($user_view_rel_content_object);

    function create_content_object_pub_feedback($content_object_publication_feedback);

    function update_content_object_pub_feedback($content_object_publication_feedback);

    function delete_content_object_pub_feedback($content_object_publication_feedback);

    function retrieve_user_view_rel_content_objects($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_content_object_pub_feedback($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_last_post($forum_id);

    function create_content_object_metadata($content_object_metadata);

    function delete_content_object_metadata($content_object_metadata);

    function update_content_object_metadata($content_object_metadata);

    function retrieve_content_object_metadata($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function retrieve_content_object_by_catalog_entry_values($catalog_name, $entry_value);

    function retrieve_external_repository($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function retrieve_external_repository_fedora($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function retrieve_catalog($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = null);

    function create_external_repository_sync_info($external_repository_sync_info);

    function update_external_repository_sync_info($external_repository_sync_info);

    function delete_external_repository_sync_info($external_repository_sync_info);

    function retrieve_doubles_in_repository($condition, $order_property, $offset, $count);

    function count_doubles_in_repository($condition);

}
?>