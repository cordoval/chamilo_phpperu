<?php

/**
 * $Id: dokeos185_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_user.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/profile/profile.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';
require_once Path :: get_application_path() . 'lib/profiler/profile_publication.class.php';

/**
 * This class represents an old Dokeos 1.8.5 user
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke 
 */

class Dokeos185User extends Import
{
    /**
     * Migration data manager
     */
    
    /**
     * Table User Properties
     */
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LASTNAME = 'lastname';
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_USERNAME = 'username';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_AUTH_SOURCE = 'auth_source';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PHONE = 'phone';
    const PROPERTY_OFFICIAL_CODE = 'official_code';
    const PROPERTY_PICTURE_URI = 'picture_uri';
    const PROPERTY_CREATOR_ID = 'creator_id';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_COMPETENCES = 'competences';
    const PROPERTY_DIPLOMAS = 'diplomas';
    const PROPERTY_OPENAREA = 'openarea';
    const PROPERTY_TEACH = 'teach';
    const PROPERTY_PRODUCTIONS = 'productions';
    const PROPERTY_CHATCALL_USER_ID = 'chatcall_user_id';
    const PROPERTY_CHATCALL_DATE = 'chatcall_date';
    const PROPERTY_CHATCALL_TEXT = 'chatcall_text';
    const PROPERTY_REGISTRATION_DATE = 'registration_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_OPENID = 'openid';
    
    /** 
     * Table Admin Properties
     */
    const PROPERTY_ADMIN = 'user_id';
    
    const ACTION_READ_USER = 'read';
    
    /**
     * Numeric identifier of the user object.
     */
    private $user_id;
    
    /**
     * Default properties of the user table, stored in an associative
     * array.
     */
    private $default_user_properties;
    
    /**
     * Default properties of the admin table, stored in an associative array
     */
    
    private $default_admin_properties;

    /**
     * Creates a new user object.
     * @param array $default_user_properties The default properties of the user
     *                                 object. Associative array.
     */
    function Dokeos185User($default_user_properties = array (), $default_admin_properties = array())
    {
        $this->default_user_properties = $default_user_properties;
        $this->default_admin_properties = $default_admin_properties;
    }

    /**
     * Gets a default property of this user object by name.
     * @param string $name The name of the property.
     */
    function get_default_user_property($name)
    {
        return $this->default_user_properties[$name];
    }

    /**
     * Gets a default property of the admin table by name
     * @param string $name The name of the property.
     */
    function get_default_admin_property($name)
    {
        return $this->default_admin_properties[$name];
    }

    /**
     * Gets the default properties of this user.
     * @return array An associative array containing the properties.
     */
    function get_default_user_properties()
    {
        return $this->default_user_properties;
    }

    /**
     * Gets the default properties of the admin
     * @return array An associative array containing the properties.
     */
    function get_default_admin_properties()
    {
        return $this->default_admin_properties;
    }

    /**
     * Get the default properties of the users.
     * @return array The property names.
     */
    static function get_default_user_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self :: PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_COMPETENCES, self :: PROPERTY_DIPLOMAS, self :: PROPERTY_OPENAREA, self :: PROPERTY_TEACH, self :: PROPERTY_PRODUCTIONS, self :: PROPERTY_CHATCALL_USER_ID, self :: PROPERTY_CHATCALL_DATE, self :: PROPERTY_CHATCALL_TEXT, self :: PROPERTY_REGISTRATION_DATE, self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_ACTIVE, self :: PROPERTY_OPENID);
    }

    /**
     * Get the default properties of the admins
     */
    static function get_default_admin_property_names()
    {
        return array(self :: PROPERTY_ADMIN);
    }

    /**
     * Sets a default property of this user by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_user_property($name, $value)
    {
        $this->default_user_properties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Sets a default property of this admin by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_admin_property($name, $value)
    {
        $this->default_admin_properties[$name] = $value;
    }

    /**
     * USER GETTERS AND SETTERS
     */
    
    /**
     * Returns the user_id of this user.
     * @return int The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_user_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the lastname of this user.
     * @return String The lastname
     */
    function get_lastname()
    {
        return $this->get_default_user_property(self :: PROPERTY_LASTNAME);
    }

    /**
     * Returns the firstname of this user.
     * @return String The firstname
     */
    function get_firstname()
    {
        return $this->get_default_user_property(self :: PROPERTY_FIRSTNAME);
    }

    /**
     * Returns the fullname of this user
     * @return string The fullname
     */
    function get_fullname()
    {
        //@todo Make format of fullname configurable somewhere
        return $this->get_firstname() . ' ' . $this->get_lastname();
    }

    /**
     * Returns the username of this user.
     * @return String The username
     */
    function get_username()
    {
        return $this->get_default_user_property(self :: PROPERTY_USERNAME);
    }

    /**
     * Returns the password of this user.
     * @return String The password
     */
    function get_password()
    {
        return $this->get_default_user_property(self :: PROPERTY_PASSWORD);
    }

    /**
     * Returns the auth_source for this user.
     * @return String The auth_source
     */
    function get_auth_source()
    {
        return $this->get_default_user_property(self :: PROPERTY_AUTH_SOURCE);
    }

    /**
     * Returns the email for this user.
     * @return String The email address
     */
    function get_email()
    {
        return $this->get_default_user_property(self :: PROPERTY_EMAIL);
    }

    /**
     * Returns the status for this user.
     * @return Int The status
     */
    function get_status()
    {
        return $this->get_default_user_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the official code for this user.
     * @return String The official code
     */
    function get_official_code()
    {
        return $this->get_default_user_property(self :: PROPERTY_OFFICIAL_CODE);
    }

    /**
     * Returns the phone number for this user.
     * @return String The phone number
     */
    function get_phone()
    {
        return $this->get_default_user_property(self :: PROPERTY_PHONE);
    }

    /**
     * Returns the Picture URI for this user.
     * @return String The URI
     */
    function get_picture_uri()
    {
        return $this->get_default_user_property(self :: PROPERTY_PICTURE_URI);
    }

    /**
     * Returns the creator ID for this user.
     * @return Int The creator ID
     */
    function get_creator_id()
    {
        return $this->get_default_user_property(self :: PROPERTY_CREATOR_ID);
    }

    /**
     * Returns the language for this user.
     * @return String the Language
     */
    function get_language()
    {
        return $this->get_default_user_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Returns the competences for this user.
     * @return String the Competences
     */
    function get_competences()
    {
        return $this->get_default_user_property(self :: PROPERTY_COMPETENCES);
    }

    /**
     * Returns the diplomas for this user.
     * @return String the Diplomas
     */
    function get_diplomas()
    {
        return $this->get_default_user_property(self :: PROPERTY_DIPLOMAS);
    }

    /**
     * Returns the openarea for this user.
     * @return String the Competences
     */
    function get_openarea()
    {
        return $this->get_default_user_property(self :: PROPERTY_OPENAREA);
    }

    /**
     * Returns teach for this user.
     * @return String Teach
     */
    function get_teach()
    {
        return $this->get_default_user_property(self :: PROPERTY_TEACH);
    }

    /**
     * Returns the productions for this user.
     * @return String the Productions
     */
    function get_productions()
    {
        return $this->get_default_user_property(self :: PROPERTY_PRODUCTIONS);
    }

    /**
     * Returns the chatcall_user_id for this user.
     * @return int the Chatcall_user_id
     */
    function get_chatcall_user_id()
    {
        return $this->get_default_user_property(self :: PROPERTY_CHATCALL_USER_ID);
    }

    /**
     * Returns the chatcall_date for this user.
     * @return String the chatcall_date
     */
    function get_chatcall_date()
    {
        return $this->get_default_user_property(self :: PROPERTY_CHATCALL_DATE);
    }

    /**
     * Returns the chatcall_text for this user.
     * @return String the Chatcall_text
     */
    function get_chatcall_text()
    {
        return $this->get_default_user_property(self :: PROPERTY_CHATCALL_TEXT);
    }

    /**
     * Returns the registration_date for this user.
     * @return String the Registration_date
     */
    function get_registration_date()
    {
        return $this->get_default_user_property(self :: PROPERTY_REGISTRATION_DATE);
    }

    /**
     * Returns the expiration_date for this user.
     * @return String the Expiration_date
     */
    function get_expiration_date()
    {
        return $this->get_default_user_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    /**
     * Returns active for this user.
     * @return int active
     */
    function get_active()
    {
        return $this->get_default_user_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Returns the openid for this user.
     * @return String the Openid
     */
    function get_openid()
    {
        return $this->get_default_user_property(self :: PROPERTY_OPENID);
    }

    /**
     * ADMIN GETTERS AND SETTERS
     */
    function get_platformadmin()
    {
        return $this->get_default_admin_property(self :: PROPERTY_ADMIN);
    }

    function set_platformadmin($platformadmin)
    {
        $this->set_default_admin_property(self :: PROPERTY_ADMIN, $platformadmin);
    }

    /**
     * Migration users, create directories, copy user pictures, migrate user profiles
     * @return User
     */
    function convert_to_lcms($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $parameters['old_mgdm'];
        
        //User parameters
        $lcms_user = new User();
        $lcms_user->set_lastname($this->get_lastname());
        $lcms_user->set_firstname($this->get_firstname());
        $lcms_user->set_username($this->get_username());
        $lcms_user->set_password($this->get_password());
        $lcms_user->set_email($this->get_email());
        $lcms_user->set_status($this->get_status());
        $lcms_user->set_platformadmin($this->get_platformadmin());
        $lcms_user->set_official_code($this->get_official_code());
        $lcms_user->set_phone($this->get_phone());
        /*
        if ($mgdm->is_language_available($this->get_language()))
            //$lcms_user->set_language($this->get_language());
        else
            //$lcms_user->set_language('english');
         */
        
        //Set user authentication method, if not available use default: platform
        if ($mgdm->is_authentication_available($this->get_auth_source()))
        {
            $lcms_user->set_auth_source($this->get_auth_source());
        }
        else
        {
            $lcms_user->set_auth_source('platform');
        }

        //Move picture to correct directory
        $old_rel_path_picture = '/main/upload/users/';
        
        if ($this->get_picture_uri())
        {
            $new_rel_path_picture = '/files/userpictures/';
            
            $picture_uri = $old_mgdm->move_file($old_rel_path_picture, $new_rel_path_picture, $this->get_picture_uri());
            if ($picture_uri)
                $lcms_user->set_picture_uri($picture_uri);
            else
                $lcms_user->set_picture_uri($this->get_picture_uri());
            
            unset($new_rel_path_picture);
            unset($old_rel_path_picture);
            unset($picture_uri);
        }
        
        // Get new id from temporary table for references
        $creator_id = $mgdm->get_id_reference($this->get_creator_id(), 'user_user');
        if ($creator_id)
            $lcms_user->set_creator_id($creator_id);
        unset($creator_id);
        
        //create user in database
        $lcms_user->create();
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_user_id(), $lcms_user->get_id(), 'user_user');
        
        // Create user directory
        //$rep_dir = '/files/repository/' . $lcms_user->get_id() . '/';
        //self :: $old_mgdm->create_directory(true, $rep_dir);

        // Convert profile fields to Profile object if the user has user profile data
        if ($this->get_competences() !== NULL || $this->get_diplomas() !== NULL || $this->get_teach() !== NULL || $this->get_openarea() !== NULL || $this->get_phone() !== NULL)
        {
        	$lcms_category_id = $mgdm->get_repository_category_by_name($lcms_user->get_id(),Translation :: get('Profile'));
        	$lcms_repository_profile = new Profile();
        	$lcms_repository_profile->set_competences($this->get_competences());
        	$lcms_repository_profile->set_diplomas($this->get_diplomas());
        	$lcms_repository_profile->set_teaching($this->get_teach());
        	$lcms_repository_profile->set_open($this->get_openarea());
        	$lcms_repository_profile->set_title(Translation :: get('Profile'));
        	$lcms_repository_profile->set_parent_id($lcms_category_id);
        	$lcms_repository_profile->set_phone($this->get_phone());
        	
        	//Create profile in database
        	$lcms_repository_profile->create();
        
        	//Publish Profile
        	$lcms_profile_publication = new ProfilePublication();
        	$lcms_profile_publication->set_profile($lcms_repository_profile->get_id());
        	$lcms_profile_publication->set_publisher($lcms_user->get_id());
        
        	//Create profile publication in database
        	$lcms_profile_publication->create();
        
        	//unset
        	unset($lcms_repository_profile);
        	unset($lcms_profile_publication);
        }
        	
        //Convert all production files to content objects
        $old_path = $old_rel_path_picture . $this->get_user_id() . '/' . $this->get_user_id() . '/';
        $directory = $old_mgdm->append_full_path(false, $old_path);
        unset($old_rel_path_picture);
        if (file_exists($directory))
        {
            $files_list = Filesystem :: get_directory_content($directory, Filesystem :: LIST_FILES);
            
            if (count($files_list) != 0)
            {
                //Create category for user in lcms
                $lcms_repository_category = new RepositoryCategory();
                $lcms_repository_category->set_id($lcms_user->get_id());
                $lcms_repository_category->set_name(Translation :: get('User'));
                $lcms_repository_category->set_parent(0);
                
                //Create category in database
                $lcms_repository_category->create();
                
                foreach ($files_list as $file)
                {
                    $file_split = split('/', $file);
                    $filename = $file_split[count($file_split) - 1];
                    $new_path = '/files/repository/' . $lcms_user->get_id() . '/';
                    
                    $filename = $old_mgdm->move_file($old_path, $new_path, $filename);
                    
                    if ($filename)
                    {
                        //Create document
                        $lcms_repository_document = new Document();
                        $lcms_repository_document->set_filename($filename);
                        $lcms_repository_document->set_path($lcms_user->get_id() . '/' . $filename);
                        $lcms_repository_document->set_filesize(filesize($file));
                        
                        //Create document in db
                        $lcms_repository_document->create();
                        
                        unset($lcms_repository_document);
                    }
                    
                    unset($file_split);
                    unset($filename);
                    unset($file);
                    unset($new_path);
                }
                
                $files_list = array();
                unset($files_list);
            }
        }
        
        unset($repository_id);
        unset($old_path);
        unset($directory);
        
        $parameters = array();
        unset($parameters);
        
        $this->default_user_properties = array();
        unset($this->default_user_properties);
        $this->default_admin_properties = array();
        unset($this->default_admin_properties);
        unset($mgdm);
        unset($old_mgdm);
        
        return $lcms_user;
    }

    /**
     * Checks if the user is valid
     * Checks if username is valid
     * @param Array $lcms_users
     * @return Boolean
     */
    function is_valid($parameters)
    {
        $lcms_users = $parameters['lcms_users'];
        $mgdm = MigrationDataManager :: get_instance();
        
        if (! $this->get_username() || ! $this->get_password() || ! $this->get_status())
        {
            $mgdm->add_failed_element($this->get_user_id(), 'dokeos_main.user');
            
            return false;
        }
        
        $index = 0;
        $user = $this->username_exists($lcms_users, $this->get_username());
        $firstuser = $user;
        $newusername = $this->get_username();
        
        if ($user)
        {
            do
            {
                $newusername = $newusername . ($index ++);
                $user = $this->username_exists($lcms_users, $newusername);
            }
            while ($user);
        }
        
        $lcms_users = array();
        unset($lcms_users);
        
        if ($firstuser)
        {
            $firstuser->set_username($newusername);
            $firstuser->update();
            /*mail($firstuser->get_email(),'Login for Dokeos changed',
			'Because we upgraded dokeos to a newer version we were obligated\r\n
			 to change your login name\r\n
			 new login: ' . $firstuser->get_username());*/
        }
        
        $parameters = array();
        unset($parameters);
        unset($firstuser);
        unset($newusername);
        unset($user);
        unset($index);
        unset($mgdm);
        return true;
    }

    /**
     * Gets all the user
     * @param migrationdatamanager $mgdm
     * @return Array
     */
    static function get_all($parameters)
    {
    	$old_mgdm = $parameters['old_mgdm'];
        return $old_mgdm->get_all_users($parameters['offset'], $parameters['limit']);
    }

    /**
     * Checks if username exists
     * @param Array $lcms_users
     * @param String $old_username
     */
    function username_exists($lcms_users, $old_username)
    {
        foreach ($lcms_users as $lcms_user)
        {
            if ($lcms_user->get_username() == $old_username)
            {
                return $lcms_user;
            }
        }
        return null;
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'user';
        return $array;
    }
}
?>
