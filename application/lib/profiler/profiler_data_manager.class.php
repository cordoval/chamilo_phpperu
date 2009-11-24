<?php
/**
 * $Id: profiler_data_manager.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
abstract class ProfilerDataManager
{

    private static $instance;

    protected function ProfilerDataManager()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'ProfilerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Determines whether any of the given learning objects has been published
     * in this application.
     * @param array $object_ids The Id's of the learning objects
     * @return boolean True if at least one of the given objects is published in
     * this application, false otherwise
     */
    abstract function any_content_object_is_published($object_ids);

    /**
     * Returns whether a given object id is published in this application
     * @param int $object_id
     * @return boolean Is the object is published
     */
    abstract function content_object_is_published($object_id);

    /**
     * Gets the publication attributes of a given learning object id
     * @param int $object_id The object id
     * @return ContentObjectPublicationAttribute
     */
    abstract function get_content_object_publication_attribute($object_id);

    /**
     * Gets the publication attributes of a given array of learning object id's
     * @param array $object_id The array of object ids
     * @param string $type Type of retrieval
     * @param int $offset
     * @param int $count
     * @param int $order_property
     * @return array An array of Learing Object Publication Attributes
     */
    abstract function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return int
     */
    abstract function count_publication_attributes($user, $type = null, $condition = null);

    abstract function initialize();

    /**
     * Count the publications
     * @param Condition $condition
     * @return int
     */
    abstract function count_profile_publications($condition = null);

    /**
     * Retrieve a profile publication
     * @param int $id
     * @return ProfilePublication
     */
    abstract function retrieve_profile_publication($id);

    /**
     * Retrieve a series of profile publications
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return ProfilePublicationResultSet
     */
    abstract function retrieve_profile_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    /**
     * Update the publication
     * @param ProfilePublication $profile_publication
     * @return boolean
     */
    abstract function update_profile_publication($profile_publication);

    /**
     * Delete the publication
     * @param ProfilePublication $profile_publication
     * @return boolean
     */
    abstract function delete_profile_publication($profile_publication);

    /**
     * Delete the publications
     * @param Array $object_id An array of publication ids
     * @return boolean
     */
    abstract function delete_profile_publications($object_id);

    /**
     * Update the publication id
     * @param ContentObjectPublicationAttribure $publication_attr
     * @return boolean
     */
    abstract function update_profile_publication_id($publication_attr);

    /**
     * Create a publication
     * @param PersonalMessagePublication $publication
     * @return boolean
     */
    abstract function create_profile_publication($publication);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function select_next_category_display_order($parent_category_id);

    abstract function delete_category($category);

    abstract function update_category($category);

    abstract function create_category($category);

    abstract function count_categories($conditions = null);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

}
?>