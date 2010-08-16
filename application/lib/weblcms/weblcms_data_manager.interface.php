<?php
/**
 * $Id: weblcms_data_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
/**
==============================================================================
 * This is a skeleton for a data manager for the Weblcms application. Data
 * managers must extend this class.
 *
 * @author Tim De Pauw
==============================================================================
 */

interface WeblcmsDataManagerInterface
{

    function retrieve_max_sort_value($table, $column, $condition = null);

    /**
     * Determines whether the given learning object has been published in this
     * application.
     * @param int $object_id The ID of the learning object.
     * @return boolean True if the object is currently published, false
     * otherwise.
     */
    function content_object_is_published($object_id);

    /**
     * Determines whether any of the given learning objects has been published
     * in this application.
     * @param array $object_ids The Id's of the learning objects
     * @return boolean True if at least one of the given objects is published in
     * this application, false otherwise
     */
    function any_content_object_is_published($object_ids);

    /**
     * Determines where in this application the given learning object has been
     * published.
     * @param int $object_id The ID of the learning object.
     * @return array An array of ContentObjectPublicationAttributes objects;
     * empty if the object has not been published anywhere.
     */
    function get_content_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null);

    /**
     * Retrieves the attributes for the given publication.
     * @param int $publication_id
     * @return array An array of ContentObjectPublicationAttributes objects;
     * empty if the object has not been published anywhere.
     */
    function get_content_object_publication_attribute($publication_id);

    function delete_courses_by_course_type_id($course_type_id);

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return int
     */
    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    /**
     * Delete the publications
     * @param Array $object_id An array of publication ids
     * @return boolean
     */
    function delete_content_object_publications($object_id);

    /**
     * Initializes the data manager.
     */
    function initialize();

    /**
     * Retrieves a single learning object publication from persistent
     * storage.
     * @param int $pid The numeric identifier of the publication.
     * @return ContentObjectPublication The publication.
     */
    function retrieve_content_object_publication($pid);

    /**
     * Retrieves learning object publications from persistent storage.
     * @param Condition $condition A Condition for publication selection. See
     * the Conditions framework.
     * @param boolean $allowDuplicates Whether or not to allow the same
     * publication to be returned twice, e.g.
     * if it was published for several course_groups
     * that the user is a member of. Defaults
     * to false.
     * @param array $order_by The properties to order publications by.
     * @param int $offset The index of the first publication to retrieve.
     * @param int $max_objects The maximum number of objects to retrieve.
     * @return ResultSet A set of ContentObjectPublications.
     */
    function retrieve_content_object_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    function count_content_object_publications($condition);

    //--Course_type_items--
    

    /**
     * Count the number of course_types
     * @param Condition $condition
     * @return int
     */
    function count_course_types($conditions = null);

    function create_course_request($request);

    function count_requests($conditions = null);

    /**
     * Creates a coursetype object in persistent storage.
     * @param CourseType $courseytype The coursetype to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_course_type($course_type);

    function create_course_type_settings($course_type_settings);

    function create_course_type_tool($course_type_tool);

    function create_course_type_layout($course_type_layout);

    function create_course_type_rights($course_type_rights);

    function create_course_type_group_subscribe_right($course_type_group_subscribe_right);

    function create_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right);

    function create_course_type_group_creation_right($course_type_group_creation_right);

    /**
     * Updates the specified course_type in persistent storage,
     * making any changes permanent.
     * @param CourseType $course_type The course_type object
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_course_type($course_type);

    function update_course_type_settings($course_type_settings);

    function update_course_type_layout($course_type_layout);

    function update_course_type_tool($course_type_tool);

    function update_course_type_rights($course_type_rights);

    function update_course_type_group_subscribe_right($course_type_group_subscribe_right);

    function update_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right);

    function update_course_type_group_creation_right($course_type_group_creation_right);

    function update_course_type_user_category($course_type_user_category);

    function delete_course_type($course_type_id);

    function delete_course_type_user_category($course_type_user_category);

    function delete_course_type_group_subscribe_right($course_type_group_subscribe_right);

    function delete_course_type_group_unsubscribe_right($course_type_group_unsubscribe_right);

    function delete_course_type_group_creation_right($course_type_group_creation_right);

    /**
     * Deletes the given course_type_tool from the database related to this given course_type.
     * @param string $course_type_tool The course_type_tool
     */
    function delete_course_type_tool($course_type_tool);

    /**
     * Retrieves a single course_type from persistent storage.
     * @param int $id
     * @return CourseType The course_type
     */
    function retrieve_course_type($id);

    function retrieve_empty_course_type();

    function retrieve_course_types($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_requests($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_active_course_types();

    function count_active_course_types();

    function retrieve_course_type_settings($id);

    function retrieve_course_type_layout($id);

    function retrieve_course_type_group_creation_right($course_type_id, $group_id);

    function count_course_type_group_creation_rights($condition = null);

    function retrieve_course_type_group_creation_rights($id);

    function retrieve_all_course_type_tools($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_course_type_user_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_course_type_user_category($condition = null);

    //-- END -- Course_type_items--
    

    /**
     * Count the number of courses
     * @param Condition $condition
     * @return int
     */
    function count_courses($condition);

    function count_requests_by_course($condition);

    /**
     * Count the number of course categories
     * @param Condition $condition
     * @return int
     */
    function count_course_categories($condition = null);

    /**
     * Count the number of courses th user is subscribed to
     * @param Condition $condition
     * @return int
     */
    function count_user_courses($conditions = null);

    /**
     * Count the number of distinct course user relations
     * @return int
     */
    function count_distinct_course_user_relations();

    /**
     * Count the number of course user relations
     * @param Condition $condition
     * @return int
     */
    function count_course_user_relations($conditions = null);

    function retrieve_request($id);

    /**
     * Count the number of course group relations
     * @param Condition $condition
     * @return int
     */
    function count_course_group_relations($conditions = null);

    /**
     * Count the number of course user categories
     * @param Condition $condition
     * @return int
     */
    function count_course_user_categories($conditions = null);

    /**
     * Creates a course object in persistent storage.
     * @param Course $course The course to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_course($course);

    function update_course_request($request);

    function create_course_settings($course_settings);

    function create_course_layout($course_layout);

    function create_course_group_user_relation($course_group_user_relation);

    function create_course_rights($course_rights);

    function create_course_group_subscribe_right($course_group_subscribe_right);

    function create_course_group_unsubscribe_right($course_group_unsubscribe_right);

    function create_course_modules($course_modules, $course_code);

    function create_course_module($course_module);

    /**
     * Creates a course category object in persistent storage.
     * @param CourseCategory $coursecategory The course category to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_course_category($coursecategory);

    /**
     * Subscribe a user to a course.
     * @param Course $course
     * @param int $status
     * @param int $tutor_id
     * @param int $user_id
     * @return boolean
     */
    function subscribe_user_to_course($course, $status, $tutor_id, $user_id);

    /**
     * Unsubscribe a user from a course.
     * @param Course $course
     * @param int $user_id
     * @return boolean
     */
    function unsubscribe_user_from_course($course, $user_id);

    /**
     * Subscribe a group to a course.
     * @param Course $course
     * @param int $group_id
     * @return boolean
     */
    function subscribe_group_to_course(Course $course, $group_id);

    /**
     * Unsubscribe a user from a course.
     * @param Course $course
     * @param int $group_id
     * @return boolean
     */
    function unsubscribe_group_from_course(Course $course, $group_id);

    /**
     * Checks whether a user is subscribed to a course.
     * @param Course $course
     * @param User $user
     * @return boolean
     */
    function is_subscribed($course, User $user);

    /**
     * Checks whether the course category exists.
     * @param string $category
     * @return boolean
     */
    function is_course_category($category);

    /**
     * Checks whether the course exists.
     * @param string $course_code
     * @return boolean
     */
    function is_course($course_code);

    /**
     * Checks whether the given user is an admin for the given course.
     * @param Course $course
     * @param int $user_id
     * @return boolean
     */
    function is_course_admin($course, $user_id);

    /**
     * Creates a course user category object in persistent storage.
     * @param CourseUserCategory $courseusercategory The course user category to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_course_user_category($courseusercategory);

    /**
     * Deletes a course user category object from persistent storage.
     * @param CourseUserCategory $courseusercategory The course user category to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function delete_course_user_category($courseusercategory);

    /**
     * Deletes a course user object from persistent storage.
     * @param CourseUserRelation $courseuser The course user to make persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    
    function delete_course_user($courseuser);

    /**
     * Creates a learning object publication in persistent storage.
     * @param ContentObjectPublication $publication The publication to make
     * persistent.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_content_object_publication($publication);

    function create_content_object_publication_user($publication_user);

    function create_content_object_publication_course_group($publication_course_group);

    /**
     * Updates a learning object publication in persistent storage.
     * @param ContentObjectPublication $publication The publication to update
     * in storage.
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_content_object_publication($publication);

    /**
     * Removes learning object publication from persistent storage.
     * @param ContentObjectPublication $publication The publication to remove
     * from storage.
     * @return boolean True if deletion succceeded, false otherwise.
     */
    function delete_content_object_publication($publication);

    /**
     * Updates a learning object publication object id in persistent storage.
     * @param ContentObjectPublicationAttribute $publication_attr The publication to update
     * in storage.
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_content_object_publication_id($publication_attr);

    /**
     * Retrieves a the list of courses a user is the admin for
     * @param int $user_id
     * @return array An array of course codes
     */
    function retrieve_course_list_of_user_as_course_admin($user_id);

    /**
     * Moves a learning object publication among its siblings.
     * @param ContentObjectPublication $publication The publication to move.
     * @param int $places The number of places to move the publication down
     * by. If negative, the publication will be moved up.
     * @return int The number of places that the publication was moved down.
     */
    function move_content_object_publication($publication, $places);

    /**
     * Returns the next available index in the display order.
     * @param string $course The course in which the publication will be
     * added.
     * @param string $tool The tool in which the publication will be added.
     * @param string $category The category in which the publication will be
     * added.
     * @return int The requested display order index.
     */
    function get_next_content_object_publication_display_order_index($course, $tool, $category);

    //
    //	/**
    //	 * Returns the available learning object publication categories for the
    //	 * given course and tools.
    //	 * @param string $course The course ID.
    //	 * @param mixed $tools The tool names. May be a string if only one.
    //	 * @param integer $root_category_id If $tools is only one tool, then only
    //	 * return the categories under this given category_id (Default: 0 = root
    //	 * category of the tools)
    //	 * @return array The publication categories.
    //	 */
    //	 function retrieve_content_object_publication_categories($course, $tools, $root_category_id = 0);
    //
    //	/**
    //	 * Retrieves a single learning object publication category by ID and
    //	 * returns it.
    //	 * @param int $id The category ID.
    //	 * @return ContentObjectPublicationCategory The category, or null if it
    //	 *                                           could not be found.
    //	 */
    function retrieve_content_object_publication_category($id);

    /**
     * Gets the course modules in a given course
     * @param string $course_code The course code
     * @return array The list of available course modules
     */
    /*    function get_course_modules($course_code);*/
    
    /**
     * Gets all course modules
     * @return array The list of available course modules
     */
    function get_all_course_modules();

    /**
     * Retrieves a single course from persistent storage.
     * @param string $course_code The alphanumerical identifier of the course.
     * @return Course The course.
     */
    function retrieve_course($course_code);

    function retrieve_empty_course();

    function retrieve_course_module($course_module_id);

    function retrieve_course_settings($course_code);

    function retrieve_course_layout($course_code);

    function retrieve_course_group_subscribe_rights($course);

    function retrieve_course_group_unsubscribe_rights($course);

    function retrieve_course_type_group_subscribe_rights($course_type);

    function retrieve_course_type_group_unsubscribe_rights($course_type);

    function retrieve_course_group_subscribe_right($course_id, $group_id);

    function retrieve_course_group_unsubscribe_right($course_id, $group_id);

    function count_course_group_subscribe_rights($condition = null);

    function count_course_group_unsubscribe_rights($condition = null);

    //   function retrieve_course_type_group_subscribe_right($course_type_id, $group_id);
    

    //   function retrieve_course_type_group_unsubscribe_right($course_type_id, $group_id);
    

    function retrieve_course_type_group_rights_by_type($course_type_id, $type);

    /**
     * Retrieve a series of courses
     * @param User $user
     * @param string $category
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return CourseResultSet
     */
    function retrieve_courses($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Retrieve a series of courses for a specific user + the relation
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return CourseResultSet
     */
    function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Updates the specified course in persistent storage,
     * making any changes permanent.
     * @param Course $course The course object
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_course($course);

    function update_course_settings($course_settings);

    function update_course_layout($course_layout);

    function update_course_module($course_module);

    function update_course_group_subscribe_right($course_group_subscribe_right);

    function update_course_group_unsubscribe_right($course_group_unsubscribe_right);

    /**
     * Updates the specified course category in persistent storage,
     * making any changes permanent.
     * @param CourseCategory $coursecategory The coursecatgory object
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_course_category($coursecategory);

    /**
     * Updates the specified course user category in persistent storage,
     * making any changes permanent.
     * @param CourseUserCategory $course The course user category object
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_course_user_category($courseusercategory);

    /**
     * Updates the specified course user relation in persistent storage,
     * making any changes permanent.
     * @param CourseUserRelation $course The course user relation object
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_course_user_relation($courseuserrelation);

    /**
     * Deletes all records from the database related to this given course.
     * @param string $course_code The course code
     */
    function delete_course($course_code);

    function delete_course_request($request_id);

    function delete_course_group_subscribe_right($course_subscribe_right);

    function delete_course_group_unsubscribe_right($course_unsubscribe_right);

    /**
     * Deletes the given course category from the database.
     * @param CourseCategory $course_category The course category
     */
    function delete_course_category($course_category);

    function delete_course_module($course_code, $course_name);

    /**
     * Sets the visibility of a course module.
     * @param string $course_code
     * @param string $module
     * @param boolean $visible
     */
    function set_module_visible($course_code, $module, $visible);

    /**
     * Retrieves a single course category from persistent storage.
     * @param string $category The numerical identifier of the course category.
     * @return CourseCategory The course category.
     */
    function retrieve_course_category($category);

    /**
     * Retrieves a single course user relation from persistent storage.
     * @param string $course_code
     * @param int $user_id
     * @return CourseCategory The course category.
     */
    function retrieve_course_user_relation($course_code, $user_id);

    /**
     * Retrieves the next course user relation according to.
     * @param int $user_id
     * @param int $category_id
     * @param int $sort
     * @param string $direction
     * @return CourseUserRelationResultSet
     */
    function retrieve_course_user_relation_at_sort($user_id, $course_type_id, $category_id, $sort, $direction);

    /**
     * Retrieves a set of course user relations
     * @param int $user_id
     * @param string $course_user_category
     */
    function retrieve_course_user_relations($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

    /**
     * Retrieves the course categories that match the criteria from persistent storage.
     * @param string $parent The parent of the course category.
     * @return DatabaseCourseCategoryResultSet The resultset of course category.
     */
    function retrieve_course_categories($parent = null);

    /**
     * Retrieves the personal course categories for a given user.
     * @return DatabaseUserCourseCategoryResultSet The resultset of course categories.
     */
    function retrieve_course_user_categories($conditions = null, $offset = null, $count = null, $order_property = null);

    function retrieve_course_user_categories_by_course_type($condition = null);

    /**
     * Retrieves a personal course category for the user.
     * @return CourseUserCategory The course user category.
     */
    function retrieve_course_user_category($condition = null);

    /**
     * Retrieves a personal course category for the user according to
     * @param int $user_id
     * @param int $sort
     * @param string $direction
     * @return CourseUserCategory The course user category.
     */
    function retrieve_course_type_user_category_at_sort($user_id, $course_type_id, $sort, $direction);

    /**
     * Adds a record to the access log of a course module
     * @param string $course_code
     * @param int $user_id
     * @param string $module_name
     * @param int $category_id
     */
    function log_course_module_access($course_code, $user_id, $module_name = null, $category_id = 0);

    /**
     * Gets the last visit date
     * @param string $course_code
     * @param string $module_name
     * @param int $category_id
     * @param int $user_id
     */
    function get_last_visit_date($course_code, $user_id, $module_name = null, $category_id = 0);

    /**
     * Deletes a course_group
     * @param int $id The course_group id
     */
    function delete_course_group($id);

    /**
     * Creates a course_group
     * @param CourseGroup $course_group
     */
    function create_course_group($course_group);

    /**
     * Updates a course_group
     * @param CourseGroup $course_group
     */
    function update_course_group($course_group);

    /**
     * Retrieves a course_group
     * @param int id
     */
    function retrieve_course_group($id);

    /**
     * Retrieves the course_groups defined in a given course
     * @param string $course_code
     */
    function retrieve_course_groups($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Retrieves the course_groups from a given course in which the given user is
     * subscribed
     * @param User The user
     * @param Course The course
     * @return DatabaseCourseGroupResultSet
     */
    function retrieve_course_groups_from_user($user, $course = null);

    /**
     * Retrieves the users in a course_group
     */
    function retrieve_course_group_users($course_group, $condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the users in a course_group
     */
    function count_course_group_users($course_group, $conditions = null);

    /**
     * Retrieves the users that can be subscribed to a course_group
     */
    function retrieve_possible_course_group_users($course_group, $condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the users that can be subscribed to a course_group
     */
    function count_possible_course_group_users($course_group, $conditions = null);

    /**
     * Subscribes users to course_groups
     * @param array|User $users A single user or an array of users
     * @param array|CourseGroup $course_groups A single course_group or an array of course_groups
     */
    function subscribe_users_to_course_groups($users, $course_groups);

    /**
     * Unsubscribes users from course_groups
     * @param array|User $users A single user or an array of users
     * @param array|CourseGroup $course_groups A single course_group or an array of course_groups
     */
    function unsubscribe_users_from_course_groups($users, $course_groups);

    /**
     * Is user member of the course_group
     */
    function is_course_group_member($course_group, $user);

    function delete_category($category);

    function update_category($category);

    function create_category($category);

    function count_categories($conditions = null);

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_content_object_publication_category($content_object_publication_category);

    function update_content_object_publication_category($content_object_publication_category);

    function create_content_object_publication_category($content_object_publication_category);

    function count_content_object_publication_categories($conditions = null);

    function retrieve_content_object_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_course_section($course_section);

    function update_course_section($course_section);

    function create_course_section($course_section);

    function count_course_sections($conditions = null);

    function retrieve_course_sections($condition = null, $offset = null, $count = null, $order_property = null);
    
    function count_new_publications_from_course($course, $user);
    
    function retrieve_course_user_categories_from_course_type($course_type_id, $user_id);
    
    function get_user_with_most_publications_in_course($course_id);

}
?>