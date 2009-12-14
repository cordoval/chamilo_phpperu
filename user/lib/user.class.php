<?php
/**
 * $Id: user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib
 */
/**
 *	This class represents a user.
 *
 *	User objects have a number of default properties:
 *	- user_id: the numeric ID of the user;
 *	- lastname: the lastname of the user;
 *	- firstname: the firstname of the user;
 *	- password: the password for this user;
 *	- auth_source:
 *  - external_uid: the external authentication system unique id of the user (eg: Shibboleth uid, OpenID uid, ...)
 *	- email: the email address of this user;
 *	- status: the status of this user: 1 is teacher, 5 is a student;
 *	- phone: the phone number of the user;
 *	- official_code; the official code of this user;
 *	- picture_uri: the URI location of the picture of this user;
 *	- creator_id: the user_id of the user who created this user;
 *	- language: the language setting of this user;
 *	- disk quota: the disk quota for this user;
 *	- database_quota: the database quota for this user;
 *	- version_quota: the default quota for this user of no quota for a specific learning object type is set.
 *
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */


class User extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_LASTNAME = 'lastname';
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_USERNAME = 'username';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_AUTH_SOURCE = 'auth_source';
    const PROPERTY_EXTERNAL_UID = 'external_uid';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PLATFORMADMIN = 'admin';
    const PROPERTY_PHONE = 'phone';
    const PROPERTY_OFFICIAL_CODE = 'official_code';
    const PROPERTY_PICTURE_URI = 'picture_uri';
    const PROPERTY_CREATOR_ID = 'creator_id';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_DISK_QUOTA = 'disk_quota';
    const PROPERTY_DATABASE_QUOTA = 'database_quota';
    const PROPERTY_VERSION_QUOTA = 'version_quota';
    const PROPERTY_THEME = 'theme';
    const PROPERTY_ACTIVATION_DATE = 'activation_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_REGISTRATION_DATE = 'registration_date';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_TIMEZONE = 'timezone';

    const ACTION_CREATE_USER = 'create';

    const NAME_FORMAT_FIRST = 0;
    const NAME_FORMAT_LAST = 1;

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EXTERNAL_UID, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PLATFORMADMIN, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self :: PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA, self :: PROPERTY_VERSION_QUOTA, self :: PROPERTY_THEME, self :: PROPERTY_ACTIVATION_DATE, self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_REGISTRATION_DATE, self :: PROPERTY_ACTIVE, self :: PROPERTY_TIMEZONE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    /**
     * Returns the lastname of this user.
     * @return String The lastname
     */
    function get_lastname()
    {
        return $this->get_default_property(self :: PROPERTY_LASTNAME);
    }

    /**
     * Returns the firstname of this user.
     * @return String The firstname
     */
    function get_firstname()
    {
        return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
    }

    /**
     * Returns the fullname of this user
     * @return string The fullname
     */
    function get_fullname()
    {
        $format = PlatformSetting :: get('fullname_format', 'user');

        switch ($format)
        {
            case self :: NAME_FORMAT_FIRST :
                return $this->get_firstname() . ' ' . $this->get_lastname();
                break;
            case self :: NAME_FORMAT_LAST :
                return $this->get_lastname() . $this->get_firstname();
                break;
            default :
                return $this->get_firstname() . ' ' . $this->get_lastname();
        }
    }

    /**
     * Returns the username of this user.
     * @return String The username
     */
    function get_username()
    {
        return $this->get_default_property(self :: PROPERTY_USERNAME);
    }

    /**
     * Returns the password of this user.
     * @return String The password
     */
    function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }

    /**
     * Returns the auth_source for this user.
     * @return String The auth_source
     */
    function get_auth_source()
    {
        return $this->get_default_property(self :: PROPERTY_AUTH_SOURCE);
    }

    /**
     * Returns the external authentication system unique id for this user
     * (useful for instance with : Shibboleth, OpenID, LDAP, ...)
     *
     * @return String The external unique id
     */
    function get_external_uid()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_UID);
    }

    /**
     * Returns the email for this user.
     * @return String The email address
     */
    function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    /**
     * Returns the status for this user.
     * @return Int The status
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns if the user is platformadmin or not.
     * @return Int platformadmin
     */
    function get_platformadmin()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORMADMIN);
    }

    /**
     * Returns the official code for this user.
     * @return String The official code
     */
    function get_official_code()
    {
        return $this->get_default_property(self :: PROPERTY_OFFICIAL_CODE);
    }

    /**
     * Returns the phone number for this user.
     * @return String The phone number
     */
    function get_phone()
    {
        return $this->get_default_property(self :: PROPERTY_PHONE);
    }

    /**
     * Returns the Picture URI for this user.
     * @return String The URI
     */
    function get_picture_uri()
    {
        return $this->get_default_property(self :: PROPERTY_PICTURE_URI);
    }

    /**
     * Returns the creator ID for this user.
     * @return Int The ID
     */
    function get_creator_id()
    {
        return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
    }

    /**
     * Returns the language for this user.
     * @return String the Language
     */
    function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Returns the disk quota for this user.
     * @return Int the disk quota
     */
    function get_disk_quota()
    {
        return $this->get_default_property(self :: PROPERTY_DISK_QUOTA);
    }

    /**
     * Returns the database quota for this user.
     * @return Int the database quota
     */
    function get_database_quota()
    {
        return $this->get_default_property(self :: PROPERTY_DATABASE_QUOTA);
    }

    /**
     * Returns the default version quota for this user.
     * @return Int the version quota
     */
    function get_version_quota()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION_QUOTA);
    }

    /**
     * Returns the default theme for this user.
     * @return string the theme
     */
    function get_theme()
    {
        return $this->get_default_property(self :: PROPERTY_THEME);
    }

    function get_activation_date()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVATION_DATE);
    }

    /**
     * Returns the expiration date for this user.
     * @return string the theme
     */
    function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    function get_registration_date()
    {
        return $this->get_default_property(self :: PROPERTY_REGISTRATION_DATE);
    }

    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Sets the lastname of this user.
     * @param String $lastname the lastname.
     */
    function set_lastname($lastname)
    {
        $this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
    }

    /**
     * Sets the firstname of this user.
     * @param String $firstname the firstname.
     */
    function set_firstname($firstname)
    {
        $this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
    }

    /**
     * Sets the username of this user.
     * @param String $username the username.
     */
    function set_username($username)
    {
        $this->set_default_property(self :: PROPERTY_USERNAME, $username);
    }

    /**
     * Sets the password of this user. If Chamilo configuration is set to encrypt
     * the password, this function will also take care of that.
     * @param String $password the password.
     */
    function set_password($password)
    {
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    /**
     * Sets the Auth_source for this user.
     * @param String $auth_source the auth source.
     */
    function set_auth_source($auth_source)
    {
        $this->set_default_property(self :: PROPERTY_AUTH_SOURCE, $auth_source);
    }

    /**
     * Sets the external authentication system unique id for this user
     * (useful for instance with : Shibboleth, OpenID, LDAP, ...)
     *
     * @param String $external_uid the external unique id
     */
    function set_external_uid($external_uid)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_UID, $external_uid);
    }

    /**
     * Sets the email for this user.
     * @param String $email the email.
     */
    function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    /**
     * Sets the status for this user.
     * @param Int $status the status.
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Sets the platformadmin property for this user.
     * @param Int $admin the platformadmin status.
     */
    function set_platformadmin($admin)
    {
        $this->set_default_property(self :: PROPERTY_PLATFORMADMIN, $admin);
    }

    /**
     * Sets the official code for this user.
     * @param String $official_code the official code.
     */
    function set_official_code($official_code)
    {
        $this->set_default_property(self :: PROPERTY_OFFICIAL_CODE, $official_code);
    }

    /**
     * Sets the phone number for this user.
     * @param String $phone the phone number
     */
    function set_phone($phone)
    {
        $this->set_default_property(self :: PROPERTY_PHONE, $phone);
    }

    /**
     * Sets the picture uri for this user object
     * @param String $picture_uri the picture URI
     */
    function set_picture_uri($picture_uri)
    {
        $this->set_default_property(self :: PROPERTY_PICTURE_URI, $picture_uri);
    }

    /**
     * Determines if this user has uploaded a picture
     * @return boolean
     */
    function has_picture()
    {
        return ((strlen($this->get_picture_uri()) > 0) && (file_exists(Path :: get(SYS_USER_PATH) . $this->get_picture_uri())));
    }

    function get_full_picture_url()
    {
        if ($this->has_picture())
        {
            return Path :: get(WEB_USER_PATH) . $this->get_picture_uri();
        }
        else
        {
            return Theme :: get_common_image_path() . 'unknown.jpg';
        }
    }

    function get_full_picture_path()
    {
        if ($this->has_picture())
        {
            return Path :: get(SYS_USER_PATH) . $this->get_picture_uri();
        }
        else
        {
            return Path :: get(SYS_IMG_PATH) . 'images/unknown.jpg';
        }
    }

    /**
     * Determines if this user has set a theme
     * @return boolean
     */
    function has_theme()
    {
        return (! is_null($this->get_theme()) ? true : false);
    }

    /**
     * Sets the picture file
     * @param array The information of the uploaded file (from the $_FILES-
     * array)
     * @todo Make image resizing configurable
     */
    function set_picture_file($file_info)
    {
        $this->delete_picture();
        $path = Path :: get(SYS_USER_PATH);
        Filesystem :: create_dir($path);
        $img_file = Filesystem :: create_unique_name($path, $this->get_id() . '-' . $this->get_fullname() . '-' . $file_info['name']);
        move_uploaded_file($file_info['tmp_name'], $path . $img_file);
        $image_manipulation = ImageManipulation :: factory($path . $img_file);
        //Scale image to fit in 400x400 box. Should be configurable somewhere
        $image_manipulation->scale(400, 400);
        $image_manipulation->write_to_file();
        $this->set_picture_uri($img_file);
    }

    /**
     * Removes the picture connected to this user
     */
    function delete_picture()
    {
        if ($this->has_picture())
        {
            $path = Path :: get(SYS_USER_PATH) . $this->get_picture_uri();
            Filesystem :: remove($path);
            $this->set_picture_uri(null);
        }
    }

    /**
     * Sets the creator ID for this user.
     * @param String $creator_id the creator ID.
     */
    function set_creator_id($creator_id)
    {
        $this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
    }

    /**
     * Sets the language for this user.
     * @param String $language The language.
     */
    function set_language($language)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

    /**
     * Sets the disk quota for this user.
     * @param Int $disk_quota The disk quota.
     */
    function set_disk_quota($disk_quota)
    {
        $this->set_default_property(self :: PROPERTY_DISK_QUOTA, $disk_quota);
    }

    /**
     * Sets the database_quota for this user.
     * @param Int $database_quota The database quota.
     */
    function set_database_quota($database_quota)
    {
        $this->set_default_property(self :: PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    /**
     * Sets the default version quota for this user.
     * @param Int $version_quota The version quota.
     */
    function set_version_quota($version_quota)
    {
        $this->set_default_property(self :: PROPERTY_VERSION_QUOTA, $version_quota);
    }

    /**
     * Sets the default theme for this user.
     * @param string $theme The theme.
     */
    function set_theme($theme)
    {
        $this->set_default_property(self :: PROPERTY_THEME, $theme);
    }

    function set_activation_date($activation_date)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVATION_DATE, $activation_date);
    }

    function get_timezone()
    {
    	return $this->get_default_property(self :: PROPERTY_TIMEZONE);
    }

	function set_timezone($timezone)
    {
    	$this->set_default_property(self :: PROPERTY_TIMEZONE, $timezone);
    }

    /**
     * Sets the default theme for this user.
     * @param string $theme The theme.
     */
    function set_expiration_date($expiration_date)
    {
        $this->set_default_property(self :: PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    function set_registration_date($registration_date)
    {
        $this->set_default_property(self :: PROPERTY_REGISTRATION_DATE, $registration_date);
    }

    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    /**
     * Gets the version type quota of a certain learning object type
     * @param String $type The learning object type
     */
    function get_version_type_quota($type)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_version_type_quota($this, $type);
    }

    /**
     * Checks if this user is a platform admin or not
     * @return boolean true if the user is a platforma admin, false otherwise
     */
    function is_platform_admin()
    {
        return ($this->get_platformadmin() == 1 ? true : false);
    }

    /**
     * Checks if this user is a teacher or not
     * @return boolean true if the user is a teacher, false otherwise
     */
    function is_teacher()
    {
        return ($this->get_status() == 1 ? true : false);
    }

    /**
     * Instructs the Datamanager to create this user.
     * @return boolean True if success, false otherwise
     */
    function create()
    {
        $udm = UserDataManager :: get_instance();
        $this->set_registration_date(time());
        $succes = $udm->create_user($this);

        $version_quota = $this->get_version_quota() ? $this->get_version_quota() : 20;

        $rdm = RepositoryDataManager :: get_instance();
        $types = $rdm->get_registered_types();

        foreach ($types as $type)
        {
            $userquota = new UserQuota();
            $userquota->set_content_object_type($type);
            $userquota->set_user_quota($version_quota);
            $userquota->set_user_id($this->get_id());
            $userquota->create();
        }

        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_groups($only_retrieve_ids = false)
    {
        $gdm = GroupDataManager :: get_instance();

        $user_groups = $gdm->retrieve_user_groups($this->get_id());

        $group_ids = array();
        while ($user_group = $user_groups->next_result())
        {
            $group_ids[] = $user_group->get_group_id();
            $group = $gdm->retrieve_group($user_group->get_group_id());
            $subgroups = $group->get_parents(false);

            while ($subgroup = $subgroups->next_result())
            {
                $subgroup_id = $subgroup->get_id();
                if (! in_array($subgroup_id, $group_ids))
                {
                    $group_ids[] = $subgroup_id;
                }
            }
        }

        if ($only_retrieve_ids)
        {
            return $group_ids;
        }

        if (count($group_ids) > 0)
        {
            $condition = new InCondition(Group :: PROPERTY_ID, $group_ids);
            return $gdm->retrieve_groups($condition);
        }
        else
        {
            return null;
        }
    }

    function get_user_groups()
    {
        $gdm = GroupDataManager :: get_instance();
        return $gdm->retrieve_user_groups($this->get_id());
    }

    function get_rights_templates()
    {
        $udm = UserDataManager :: get_instance();
        $condition = new EqualityCondition(UserRightsTemplate :: PROPERTY_USER_ID, $this->get_id());

        return $udm->retrieve_user_rights_templates($condition);
    }

    function add_rights_template_link($rights_template_id)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->add_rights_template_link($this, $rights_template_id);
    }

    function delete_rights_template_link($rights_template_id)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->delete_rights_template_link($this, $rights_template_id);
    }

    function update_rights_template_links($rights_templates)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->update_rights_template_links($this, $rights_templates);
    }

    function get_status_name()
    {
        if ($this->get_platformadmin() == '1')
        {
            return Translation :: get('PlatformAdmin');
        }
        if ($this->get_status() == '1')
        {
            return Translation :: get('CourseAdmin');
        }
        else
        {
            return Translation :: get('Student');
        }
    }

    function get_fullname_format_options()
    {
        $options = array();
        $options[self :: NAME_FORMAT_FIRST] = Translation :: get('FirstName') . ' ' . Translation :: get('LastName');
        $options[self :: NAME_FORMAT_LAST] = Translation :: get('LastName') . ' ' . Translation :: get('FirstName');
        return $options;
    }
}
?>