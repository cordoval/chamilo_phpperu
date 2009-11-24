<?php
/**
 * $Id: user_data_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */
//require_once Path :: get_application_path().'/lib/weblcms/data_manager/database.class.php';
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.

 */
abstract class UserDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Array which contains the registered applications running on top of this
	 * userdatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function UserDataManager()
	{
		$this->initialize();
	}

	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return UserDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'UserDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Deletes the given user from the persistant storage
	 * @param User $user The user.
	 */
	abstract function delete_user($user);
	/**
	 * Deletes all users from the persistant storage
	 */
	abstract function delete_all_users();

	/**
	 * Updates the given user in persistent storage.
	 * @param User $user The user.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_user($user);

	/**
	 * Updates the given user quota in persistent storage.
	 * @param object $user_quota
		 */
	abstract function update_user_quota($user_quota);

	/**
	 * Makes the given User persistent.
	 * @param User $user The user.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_user($user);

	/**
	 * Creates a storage unit.
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name, $properties, $indexes);

	/**
	 * Retrieves a user.
	 * @param $id the user ID to retrieve the info from
	 * @return User
	 */
	abstract function retrieve_user($id);
	/**
	 * Logs a user in to the platform
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password = null)
	{
		// If username is available, try to login
		if (!$this->is_username_available($username))
		{
			$user = $this->retrieve_user_by_username($username);
			$authentication_method = $user->get_auth_source();
			$authentication = Authentication::factory($authentication_method);
			$message = $authentication->check_login($user, $username, $password);
			if ($message == 'true')
			{
				return $user;
			}
			return $message;
		}
		// If username is not available, check if an authentication method is able to register
		// the user in the platform
		else
		{
			$authentication_dir = dir(Path :: get_library_path().'authentication/');
			while (false !== ($authentication_method = $authentication_dir->read()))
			{
				if(strpos($authentication_method,'.') === false && is_dir($authentication_dir->path.'/'.$authentication_method) && PlatformSetting :: get('enable_' . $authentication_method))
				{
					$authentication_class_file = $authentication_dir->path.'/'.$authentication_method.'/'.$authentication_method.'_authentication.class.php';
					$authentication_class = ucfirst($authentication_method).'Authentication';
					require_once $authentication_class_file;
					$authentication = new $authentication_class;
					if($authentication->can_register_new_user())
					{
						if($authentication->register_new_user($username,$password))
						{
							$authentication_dir->close();
							return $this->retrieve_user_by_username($username);
						}
					}
				}
			}
			$authentication_dir->close();
			return Translation :: get('UsernameNotAvailable');
		}
	}
	/**
	 * Logs the user out of the system
	 */
	public function logout()
	{
		$user = $this->retrieve_user(Session :: get_user_id());
		$authentication_method = $user->get_auth_source();
		$authentication_class_file = Path :: get_library_path().'authentication/'.$authentication_method.'/'.$authentication_method.'_authentication.class.php';
		$authentication_class = ucfirst($authentication_method).'Authentication';
		require_once $authentication_class_file;
		$authentication = new $authentication_class;
		if ($authentication->logout($user))
		{
			return true;
		}
		return false;
	}
	/**
	 * Retrieves a user by his or her username.
	 * @param $username the username to retrieve the info from
	 * @return User|null
	 */
	abstract function retrieve_user_by_username($username);

	/**
	 * Retrieves users by their email-address.
	 * @param $email the email to retrieve the info from
	 * @return array Array of users which have the given email-address
	 */
	abstract function retrieve_users_by_email($email);

	/**
	 * Retrieves users.
	 */
	abstract function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null);

	/**
	 * Counts the amount of users currently in the database
	 * @param $conditions optional conditions
	 */
	abstract function count_users($conditions = null);

	/**
	 * Retrieves the version type quota
	 * @param $user The user
	 * @param $type quota type
	 */
	abstract function retrieve_version_type_quota($user, $type);

	/**
	 * Checks whether the user is allowed to be deleted
	 * Unfinished.
	 */
	function user_deletion_allowed($user)
	{
		// TODO: Check if the user can be deleted (fe: can an admin delete another admin etc)

        //A check to not delete a user when he's an active teacher
//        {
//            $courses = WebLcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition(Course :: PROPERTY_TITULAR,$user->get_id()))->size();
//            if($courses>0)
//            return false;
//        }



        return true;
	}

	/**
	 * Checks whether this username is available in the database
	 * @param string $username The username to be checked
	 * @param int $user_id If not null, the function will check if the given
	 * username is available for the given user. If the given username of the
	 * user with this id is the same as the current username of the user, this
	 * function will return true
	 * @return boolean True if the username is available, false if not.
	 */
	abstract function is_username_available($username, $user_id = null);

	abstract function retrieve_user_rights_templates($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function create_user_rights_template($user_rights_template);

	abstract function delete_user_rights_templates($condition);

	abstract function add_rights_template_link($group, $rights_template_id);

	abstract function delete_rights_template_link($group, $rights_template_id);

	abstract function update_rights_template_links($group, $rights_templates);

	abstract function get_database();

	abstract function create_buddy_list_category($buddy_list_category);
	abstract function update_buddy_list_category($buddy_list_category);
	abstract function delete_buddy_list_category($buddy_list_category);
	abstract function retrieve_buddy_list_categories($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function create_buddy_list_item($buddy_list_item);
	abstract function update_buddy_list_item($buddy_list_item);
	abstract function delete_buddy_list_item($buddy_list_item);
	abstract function retrieve_buddy_list_items($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function create_chat_message($chat_message);
	abstract function update_chat_message($chat_message);
	abstract function delete_chat_message($chat_message);
	abstract function retrieve_chat_messages($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function retrieve_user_by_fullname($fullname);
}
?>