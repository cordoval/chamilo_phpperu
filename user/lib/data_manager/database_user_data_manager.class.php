<?php
/**
 * $Id: database_user_data_manager.class.php 231 2009-11-16 09:53:00Z vanpouckesven $
 * @package user.lib.data_manager
 */
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../user_data_manager_interface.class.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *	@author Hans De Bisschop
==============================================================================
 */

class DatabaseUserDataManager extends Database implements UserDataManagerInterface
{
	const ALIAS_USER = 'user';

	/**
	 * Initializes the connection
	 */
	function initialize()
	{
	    parent :: initialize();
		$this->set_aliases(array(User :: get_table_name() => self :: ALIAS_USER,'user_quota' => 'uq', 'user_rights_template' => 'urt', 'buddy_list_category' => 'blc', 'buddy_list_item' => 'bli'));
		$this->set_prefix('user_');
	}

	function update_user($user)
	{
		$condition = new EqualityCondition(User :: PROPERTY_ID, $user->get_id());
		return $this->update($user, $condition);
	}

	function update_user_quota($user_quota)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_USER_ID, $user_quota->get_user_id());
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_CONTENT_OBJECT_TYPE, $user_quota->get_content_object_type());
		$condition = new AndCondition($conditions);

		return $this->update($user_quota, $condition);
	}

	function create_user_quota($user_quota)
	{
		return $this->create($user_quota);
	}

	function delete_user($user)
	{
		// @Todo: review the user's objects on deletion
		// (currently: when the user is deleted, the user's objects remain, and refer to an invalid user)
		$condition = new EqualityCondition(User :: PROPERTY_ID, $user->get_id());
		return $this->delete($user->get_table_name(), $condition);
	}

	function delete_user_rights_templates($condition)
	{
		return $this->delete(UserRightsTemplate :: get_table_name(), $condition);
	}

	function delete_user_rights_template($user_rights_template)
	{
	    $conditions  = array();
	    $conditions[] = new EqualityCondition(UserRightsTemplate :: PROPERTY_USER_ID, $user_rights_template->get_user_id());
	    $conditions[] = new EqualityCondition(UserRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $user_rights_template->get_rights_template_id());
	    $condition   = new AndCondition($conditions);

		return $this->delete($user_rights_template->get_table_name(), $condition);
	}

	function create_user_rights_template($user_rights_template){
		return $this->create($user_rights_template);
	}

	function delete_all_users()
	{
		$users = $this->retrieve_users()->as_array();
		foreach($users as $index => $user)
		{
			$this->delete_user($user);
		}
	}

	function create_user($user)
	{
		//add rss id to user
		$user->set_security_token(sha1(time().uniqid()));
		$this->create($user);

		// Create the user's root category for the repository
		RepositoryRights :: create_user_root($user);

		return true;
	}

	function retrieve_user($id)
	{
		$condition = new EqualityCondition(User :: PROPERTY_ID, $id);
		return $this->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_user_by_username($username)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME, $username);
		return $this->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_user_by_external_uid($external_uid)
	{
		$condition = new EqualityCondition(User :: PROPERTY_EXTERNAL_UID, $external_uid);
		return $this->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_user_by_security_token($security_token)
	{
		$condition = new EqualityCondition(User :: PROPERTY_SECURITY_TOKEN, $security_token);
		return $this->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_users_by_email($email)
	{
		$condition = new EqualityCondition(User :: PROPERTY_EMAIL, $email);
		$users = $this->retrieve_objects(User :: get_table_name(), $condition);
		return $users->as_array();
	}

	//Inherited.
	function is_username_available($username, $user_id = null)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
		if($user_id)
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
			$conditions = new EqualityCondition(User :: PROPERTY_ID, $user_id);
			$condition = new AndCondition($conditions);
		}
		return !($this->count_objects(User :: get_table_name(), $condition) == 1);
	}

	function retrieve_user_info($username)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
		return $this->retrieve_users($condition)->next_result();
	}

	function count_users($condition = null)
	{
		if($condition)
		{
			$conditions[] = $condition;
		}

		$conditions[] = new EqualityCondition(User :: PROPERTY_APPROVED, 1);
		$condition = new AndCondition($conditions);

		return $this->count_objects(User :: get_table_name(), $condition);
	}

	function count_approval_users($condition = null)
	{
		if($condition)
		{
			$conditions[] = $condition;
		}

		$conditions[] = new EqualityCondition(User :: PROPERTY_APPROVED, 0);
		$condition = new AndCondition($conditions);

		return $this->count_objects(User :: get_table_name(), $condition);
	}

	function retrieve_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		if($condition)
		{
			$conditions[] = $condition;
		}

		$conditions[] = new EqualityCondition(User :: PROPERTY_APPROVED, 1);
		$condition = new AndCondition($conditions);

		return $this->retrieve_objects(User :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function retrieve_approval_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		if($condition)
		{
			$conditions[] = $condition;
		}

		$conditions[] = new EqualityCondition(User :: PROPERTY_APPROVED, 0);
		$condition = new AndCondition($conditions);

		return $this->retrieve_objects(User :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	//Inherited.
	function retrieve_version_type_quota($user, $type)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_USER_ID, $user->get_id());
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_CONTENT_OBJECT_TYPE, $type);
		$condition = new AndCondition($conditions);

		$version_type_quota_set = $this->count_objects(UserQuota :: get_table_name(), $condition) > 0;

		if ($version_type_quota_set)
		{
			$user_quotum = $this->retrieve_object(UserQuota :: get_table_name(), $condition);
			return $user_quotum->get_user_quota();
		}
		else
		{
			return null;
		}
	}

	function retrieve_user_rights_templates($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(UserRightsTemplate :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function add_rights_template_link($user, $rights_template_id)
	{
		$props = array();
		$props[UserRightsTemplate :: PROPERTY_USER_ID] = $user->get_id();
		$props[UserRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID] = $rights_template_id;
		$this->get_connection()->loadModule('Extended');
		return $this->get_connection()->extended->autoExecute($this->get_table_name(UserRightsTemplate :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT);
	}

	function delete_rights_template_link($user, $rights_template_id)
	{
		$conditions = array();
		$conditions = new EqualityCondition(UserRightsTemplate :: PROPERTY_USER_ID, $user->get_id());
		$conditions = new EqualityCondition(UserRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_id);
		$condition = new AndCondition($conditions);

		return $this->delete(UserRightsTemplate :: get_table_name(), $condition);
	}

	function update_rights_template_links($user, $rights_templates)
	{
		// Delete the no longer existing links
		$conditions = array();
		$conditions = new NotCondition(new InCondition(UserRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_templates));
		$conditions = new EqualityCondition(UserRightsTemplate :: PROPERTY_USER_ID, $user->get_id());
		$condition = new AndCondition($conditions);

		$success = $this->delete(UserRightsTemplate :: get_table_name(), $condition);
		if (!$success)
		{
			return false;
		}

		// Get the group's rights_templates
		$condition = new EqualityCondition(UserRightsTemplate :: PROPERTY_USER_ID, $user->get_id());
		$user_rights_templates = $this->retrieve_user_rights_templates($condition);
		$existing_rights_templates = array();

		while($user_rights_template = $user_rights_templates->next_result())
		{
			$existing_rights_templates[] = $user_rights_template->get_rights_template_id();
		}

		// Add the new links
		foreach ($rights_templates as $rights_template)
		{
			if (!in_array($rights_template, $existing_rights_templates))
			{
				if (!$this->add_rights_template_link($user, $rights_template))
				{
					return false;
				}
			}
		}

		return true;
	}

	function create_buddy_list_category($buddy_list_category)
	{
		return $this->create($buddy_list_category);
	}

	function update_buddy_list_category($buddy_list_category)
	{
		$condition = new EqualityCondition(BuddyListCategory :: PROPERTY_ID, $buddy_list_category->get_id());
		return $this->update($buddy_list_category, $condition);
	}

	function delete_buddy_list_category($buddy_list_category)
	{
		$condition = new EqualityCondition(BuddyListCategory :: PROPERTY_ID, $buddy_list_category->get_id());
		$succes = $this->delete(BuddyListCategory :: get_table_name(), $condition);

		$query = 'UPDATE '.$this->escape_table_name('buddy_list_item').' SET '.
				 $this->escape_column_name(BuddyListItem :: PROPERTY_CATEGORY_ID).'=0 WHERE'.
				 $this->escape_column_name(BuddyListItem :: PROPERTY_CATEGORY_ID).'=' . $this->quote($buddy_list_category->get_id());

		$res = $this->query($query);
		$res->free();
        return $succes;
	}

	function retrieve_buddy_list_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->retrieve_objects(BuddyListCategory :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function create_buddy_list_item($buddy_list_item)
	{
		return $this->create($buddy_list_item);
	}

	function update_buddy_list_item($buddy_list_item)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $buddy_list_item->get_user_id());
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $buddy_list_item->get_buddy_id());
		$condition = new AndCondition($conditions);

		return $this->update($buddy_list_item, $condition);
	}

	function delete_buddy_list_item($buddy_list_item)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $buddy_list_item->get_user_id());
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $buddy_list_item->get_buddy_id());
		$condition = new AndCondition($conditions);

		return $this->delete(BuddyListItem :: get_table_name(), $condition);
	}

	function retrieve_buddy_list_items($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->retrieve_objects(BuddyListItem :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function create_chat_message($chat_message)
	{
		return $this->create($chat_message);
	}

	function update_chat_message($chat_message)
	{
		$condition = new EqualityCondition(ChatMessage :: PROPERTY_ID, $chat_message->get_id());
		return $this->update($chat_message, $condition);
	}

	function delete_chat_message($chat_message)
	{
		$condition = new EqualityCondition(ChatMessage :: PROPERTY_ID, $chat_message->get_id());
		return $this->delete(ChatMessage :: get_table_name(), $condition);
	}

	function retrieve_chat_messages($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->retrieve_objects(ChatMessage :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	function retrieve_user_by_fullname($fullname)
	{
		$name = explode(' ', $fullname);
		$firstname = $name[0];
		$lastname = $name[1];

		$conditions = array();
		$conditions1 = array();
		$conditions2 = array();

		$conditions1[] = new EqualityCondition(User :: PROPERTY_FIRSTNAME, $firstname);
		$conditions1[] = new EqualityCondition(User :: PROPERTY_LASTNAME, $lastname);
		$conditions[] = new AndCondition($conditions1);

		$conditions2[] = new EqualityCondition(User :: PROPERTY_FIRSTNAME, $lastname);
		$conditions2[] = new EqualityCondition(User :: PROPERTY_LASTNAME, $firstname);
		$conditions[] = new AndCondition($conditions2);

		$condition = new OrCondition($conditions);
		$object = $this->retrieve_object(User :: get_table_name(), $condition);

		return $object;
	}

	function create_user_setting($user_setting)
	{
		return $this->create($user_setting);
	}

	function update_user_setting($user_setting)
	{
		$condition = new EqualityCondition(UserSetting :: PROPERTY_ID, $user_setting->get_id());
		return $this->update($user_setting, $condition);
	}

	function delete_user_setting($user_setting)
	{
		$condition = new EqualityCondition(UserSetting :: PROPERTY_ID, $user_setting->get_id());
		return $this->delete(UserSetting :: get_table_name(), $condition);
	}
	
	function delete_user_settings($condition = null)
    {
        return $this->delete_objects(UserSetting :: get_table_name(), $condition);
    }

	function retrieve_user_settings($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->retrieve_objects(UserSetting :: get_table_name(), $condition, $offset, $count, $order_property, UserSetting :: get_class_name());
	}

	function retrieve_user_setting($user_id, $setting_id)
	{
		$conditions[] = new EqualityCondition(UserSetting :: PROPERTY_SETTING_ID, $setting_id);
		$conditions[] = new EqualityCondition(UserSetting :: PROPERTY_USER_ID, $user_id);
		$condition = new AndCondition($conditions);

		return $this->retrieve_object(UserSetting :: get_table_name(), $condition, null, UserSetting :: get_class_name());
	}

	function count_user_settings($condition = null)
	{
		return $this->count_objects(UserSetting :: get_table_name(), $condition);
	}

        function user_deletion_allowed()
	{
		//TODO: implement method (dummy code to avoid error)
            return true;
	}
}
?>