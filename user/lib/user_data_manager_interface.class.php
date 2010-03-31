<?php
/**
 * @package group.lib
 *
 * This is an interface for a data manager for the User application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface UserDataManagerInterface
{

    function initialize();

    /**
     * Deletes the given user from the persistant storage
     * @param User $user The user.
     */
    function delete_user($user);

    /**
     * Deletes all users from the persistant storage
     */
    function delete_all_users();

    /**
     * Updates the given user in persistent storage.
     * @param User $user The user.
     * @return boolean True if the update succceeded, false otherwise.
     */
    function update_user($user);

    /**
     * Updates the given user quota in persistent storage.
     * @param object $user_quota
     */
    function update_user_quota($user_quota);

    /**
     * Makes the given User persistent.
     * @param User $user The user.
     * @return boolean True if creation succceeded, false otherwise.
     */
    function create_user($user);

    /**
     * Creates a storage unit.
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

    /**
     * Retrieves a user.
     * @param $id the user ID to retrieve the info from
     * @return User
     */
    function retrieve_user($id);

    /**
     * Retrieves a user by his or her username.
     * @param $username the username to retrieve the info from
     * @return User|null
     */
    function retrieve_user_by_username($username);

    /**
     * Retrieves a user by his or her security token.
     * @param $security_token the security token to retrieve the info from
     * @return User|null
     */
    function retrieve_user_by_security_token($security_token);

    /**
     * Retrieves users by their email-address.
     * @param $email the email to retrieve the info from
     * @return array Array of users which have the given email-address
     */
    function retrieve_users_by_email($email);

    /**
     * Retrieves users.
     */
    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the amount of users currently in the database
     * @param $conditions optional conditions
     */
    function count_users($conditions = null);

    /**
     * Retrieves the version type quota
     * @param $user The user
     * @param $type quota type
     */
    function retrieve_version_type_quota($user, $type);

    /**
     * Checks whether this username is available in the database
     * @param string $username The username to be checked
     * @param int $user_id If not null, the function will check if the given
     * username is available for the given user. If the given username of the
     * user with this id is the same as the current username of the user, this
     * function will return true
     * @return boolean True if the username is available, false if not.
     */
    function is_username_available($username, $user_id = null);

    function retrieve_user_rights_templates($condition = null, $offset = null, $count = null, $order_property = null);

    function create_user_rights_template($user_rights_template);

    function delete_user_rights_templates($condition);

    function add_rights_template_link($group, $rights_template_id);

    function delete_rights_template_link($group, $rights_template_id);

    function update_rights_template_links($group, $rights_templates);

    function create_buddy_list_category($buddy_list_category);

    function update_buddy_list_category($buddy_list_category);

    function delete_buddy_list_category($buddy_list_category);

    function retrieve_buddy_list_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function create_buddy_list_item($buddy_list_item);

    function update_buddy_list_item($buddy_list_item);

    function delete_buddy_list_item($buddy_list_item);

    function retrieve_buddy_list_items($condition = null, $offset = null, $count = null, $order_property = null);

    function create_chat_message($chat_message);

    function update_chat_message($chat_message);

    function delete_chat_message($chat_message);

    function retrieve_chat_messages($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_user_by_fullname($fullname);

    function create_user_setting($user_setting);

    function update_user_setting($user_setting);

    function delete_user_setting($user_setting);

    function retrieve_user_settings($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_user_setting($user_id, $setting_id);

    function count_user_settings($condition = null);
}
?>