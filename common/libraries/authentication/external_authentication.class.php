<?php
/**
 * @package common.authentication
 */
require_once dirname(__FILE__) . '/../global.inc.php';
require_once Path :: get_rights_path() . 'lib/rights_manager/component/right_requester.class.php';

/**
 * This class gives basic functionalities for authentication with external authentication systems.
 *
 * The children classes may also implement user attributes retrieval from the external authentication system.
 *
 */
abstract class ExternalAuthentication extends Authentication
{
    const USER_OBJECT_CLASSNAME = 'User';

    const AUTHENTICATION_SOURCE_NAME = 'AUTHENTICATION_METHOD_NAME';
    const ALLOW_AUTOMATIC_REGISTRATION = 'ALLOW_AUTOMATIC_REGISTRATION';
    const ALLOW_USER_REGISTRATION_WITHOUT_ROLE = 'ALLOW_USER_REGISTRATION_WITHOUT_ROLE';
    const FIELDS_TO_UPDATE_AT_LOGIN = 'FIELDS_TO_UPDATE_AT_LOGIN';
    const USER_ATTRIBUTES_MAPPING = 'USER_ATTRIBUTES_MAPPING';
    const USER_ROLE_ATTRIBUTE_MAPPING = 'USER_ROLE_ATTRIBUTE_MAPPING';
    const LET_USER_ASK_RIGHT_WHEN_UNCLEAR = 'LET_USER_ASK_RIGHT_WHEN_UNCLEAR';

    const PARAM_MAPPING_EXTERNAL_UID = 'external_uid';
    const PARAM_MAPPING_FIRSTNAME = 'firstname';
    const PARAM_MAPPING_LASTNAME = 'lastname';
    const PARAM_MAPPING_EMAIL = 'email';
    const PARAM_MAPPING_AFFILIATION = 'affiliation';
    const PARAM_MAPPING_LANG = 'lang';

    const ENGLISH = 'english';
    const FRENCH = 'french';
    const DUTCH = 'dutch';
    const SPANISH = 'spanish';

    /**
     * Array used to store the authentication configuration
     */
    private $parameters = array();

    /**
     * Constructor
     */
    function ExternalAuthentication()
    {
        parent :: Authentication();

        $this->initialize();
    }

    /**
     * Initialization with default values
     */
    protected function initialize()
    {
        $this->set_automatic_registration(false);

        $this->initialize_user_attributes_mapping();
        $this->initialize_role_attributes_mapping();
    }

    /*************************************************************************/
    /*************************************************************************/

    abstract protected function initialize_user_attributes_mapping();

    abstract protected function initialize_fields_to_update_at_login();

    abstract protected function initialize_role_attributes_mapping();

    /**
     * Set the user attributes based on the external identity provider
     *
     * @param User $user
     * @param $fields_to_set If given, contains the user's fields that must be set. If null, all user's fields are set
     * @return User The given user, with new attributes set
     */
    abstract protected function set_user_attributes($user, $fields_to_set = null);

    /**
     * Sets the user role / group affiliation.
     * The user must be an existing user, meaning he must have an existing id
     *
     * @param User $user The user to put in group(s) or on which role(s) must be granted
     * @return Boolean role(s) / group(s) affiliation result
     */
    abstract protected function set_user_rights($user);

    /*************************************************************************/
    /*************************************************************************/

    /**
     * Set the authentication source name (stored in the 'auth_source' user's field)
     *
     * @param string $authentication_method_name
     */
    function set_authentication_source_name($authentication_source_name)
    {
        if (isset($authentication_source_name) && strlen($authentication_source_name) > 0)
        {
            $this->set_config_parameter(self :: AUTHENTICATION_SOURCE_NAME, $authentication_source_name);
        }
    }

    /**
     * Get the authentication source name (stored in the 'auth_source' user's field)
     *
     * @return string
     */
    function get_authentication_source_name()
    {
        $auth_source = $this->get_config_parameter(self :: AUTHENTICATION_SOURCE_NAME);
        if (isset($auth_source))
        {
            return $this->get_config_parameter(self :: AUTHENTICATION_SOURCE_NAME);
        }
        else
        {
            /*
             * The field in DB has a default value of 'platform'
             * -> probably better the value 'unknown' as it could be easily identified if ever stored in DB
             */

            return 'unknown';
        }
    }

    /**
     * Enable / disable the automatic account creation for new users
     *
     * @param bool $enabled
     */
    protected function set_automatic_registration($enabled)
    {
        if (is_bool($enabled))
        {
            $this->set_config_parameter(self :: ALLOW_AUTOMATIC_REGISTRATION, $enabled);
        }
        else
        {
            $this->set_config_parameter(self :: ALLOW_AUTOMATIC_REGISTRATION, false);
        }
    }

    /**
     * Check if the automatic account creation for new users is enabled
     *
     * @return bool
     */
    protected function is_automatic_registration_enabled()
    {
        $enabled = $this->get_config_parameter(self :: ALLOW_AUTOMATIC_REGISTRATION);

        if (isset($enabled) && is_bool($enabled))
        {
            return $enabled;
        }
        else
        {
            return false;
        }
    }

    /**
     * Enable / disable the automatic user registration when no role
     * information could be found
     *
     * @param bool $enabled
     */
    protected function set_register_user_without_role($enabled)
    {
        if (is_bool($enabled))
        {
            $this->set_config_parameter(self :: ALLOW_USER_REGISTRATION_WITHOUT_ROLE, $enabled);
        }
        else
        {
            $this->set_config_parameter(self :: ALLOW_USER_REGISTRATION_WITHOUT_ROLE, false);
        }
    }

    /**
     * Check if the automatic user registration when no role information could be found is enabled
     *
     * @return unknown
     */
    protected function is_user_without_role_registration_enabled()
    {
        $enabled = $this->get_config_parameter(self :: ALLOW_USER_REGISTRATION_WITHOUT_ROLE);

        if (isset($enabled) && is_bool($enabled))
        {
            return $enabled;
        }
        else
        {
            return false;
        }
    }

    /**
     * This method is used to initialize the attribute mapping used to set user attributes
     * when they are created or updated, depending on the values given by the external
     * identity provider (Shibboleth, OpenID, LDAP, ...)
     *
     * @param array $attributes_mapping An array of [attribute name] <--> [user property] pairs
     */
    protected function set_user_attribute_mapping($attributes_mapping = array())
    {
        if (is_array($attributes_mapping))
        {
            $this->set_config_parameter(self :: USER_ATTRIBUTES_MAPPING, $attributes_mapping);
        }
        else
        {
            $this->set_config_parameter(self :: USER_ATTRIBUTES_MAPPING, array());
        }
    }

    /**
     * Check if the user attribute mapping array is initialized and contains informations
     *
     * @return bool
     */
    protected function has_user_attribute_mapping()
    {
        $user_attribute_mapping = $this->get_user_attribute_mapping();

        if (isset($user_attribute_mapping) && is_array($user_attribute_mapping) && count($user_attribute_mapping) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the user attribute mapping array
     *
     * @return array
     */
    protected function get_user_attribute_mapping()
    {
        return $this->get_config_parameter(self :: USER_ATTRIBUTES_MAPPING);
    }

    /**
     * This method is used to initialize which fields must be updated with
     * the external identity provider values when an existing user logs in.
     *
     * Note that any modification made in Chamilo are reset for the given
     * fields when the user logs in again.
     *
     * @param array $fields_to_update The fields that are reset when a user logs in
     */
    protected function set_fields_to_update_at_login($fields_to_update = array())
    {
        if (is_array($fields_to_update))
        {
            $this->set_config_parameter(self :: FIELDS_TO_UPDATE_AT_LOGIN, $fields_to_update);
        }
        elseif (is_string($fields_to_update))
        {
            $this->set_config_parameter(self :: FIELDS_TO_UPDATE_AT_LOGIN, array($fields_to_update));
        }
        else
        {
            $this->set_config_parameter(self :: FIELDS_TO_UPDATE_AT_LOGIN, array());
        }
    }

    /**
     * Checks if some user fields must be updated when a user logs in again
     *
     * @return bool
     */
    protected function has_fields_to_update_at_login()
    {
        $fields_to_update_at_login = $this->get_fields_to_update_at_login();

        if (isset($fields_to_update_at_login) && is_array($fields_to_update_at_login) && count($fields_to_update_at_login) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the fields that must be updated when a user logs in again
     *
     * @return array
     */
    protected function get_fields_to_update_at_login()
    {
        return $this->get_config_parameter(self :: FIELDS_TO_UPDATE_AT_LOGIN);
    }

    /**
     * This method is used to initialize the attribute mapping used to
     * put new users in group / roles when they are created, depending on
     * the values given by the external identity provider (Shibboleth, OpenID, LDAP, ...)
     *
     * @param array $role_mapping An array of [user's attribute] <--> [Chamilo group / role ] pairs
     */
    protected function set_user_role_attribute_mapping($role_mapping = array())
    {
        if (is_array($role_mapping))
        {
            $this->set_config_parameter(self :: USER_ROLE_ATTRIBUTE_MAPPING, $role_mapping);
        }
        else
        {
            $this->set_config_parameter(self :: USER_ROLE_ATTRIBUTE_MAPPING, array());
        }
    }

    /**
     * Check if the user attribute role mapping array is initialized and contains informations
     *
     * @return bool
     */
    protected function has_user_role_attribute_mapping()
    {
        $user_role_attribute_mapping = $this->get_user_role_attribute_mapping();

        if (isset($user_role_attribute_mapping) && is_array($user_role_attribute_mapping) && count($user_role_attribute_mapping) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the user attribute role mapping array
     *
     * @return array
     */
    protected function get_user_role_attribute_mapping()
    {
        return $this->get_config_parameter(self :: USER_ROLE_ATTRIBUTE_MAPPING);
    }

    /**
     * Enable / disable the possibility for the user to ask for rights when its rights status is unclear
     *
     * @param bool $enabled
     */
    protected function set_display_rights_form_when_unclear_status($enabled)
    {
        if (is_bool($enabled))
        {
            $this->set_config_parameter(self :: LET_USER_ASK_RIGHT_WHEN_UNCLEAR, $enabled);
        }
        else
        {
            $this->set_config_parameter(self :: LET_USER_ASK_RIGHT_WHEN_UNCLEAR, false);
        }
    }

    /**
     * Check if the possibility for the user to ask for rights when its rights status is unclear is enabled
     *
     * @return bool
     */
    protected function get_display_rights_form_when_unclear_status()
    {
        $enabled = $this->get_config_parameter(self :: LET_USER_ASK_RIGHT_WHEN_UNCLEAR);

        if (isset($enabled) && is_bool($enabled))
        {
            return $enabled;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set a configuration value for the given parameter name
     *
     * @param string $parameter_name Name of the parameter to set
     * @param string $parameter_value Value of the parameter
     */
    protected function set_config_parameter($parameter_name, $parameter_value)
    {
        $this->parameters[$parameter_name] = $parameter_value;
    }

    /**
     * Gets a configuration value for the given parameter name
     *
     * @param string $parameter_name
     * @return string
     */
    protected function get_config_parameter($parameter_name)
    {
        return $this->parameters[$parameter_name];
    }

    /*************************************************************************/
    /*************************************************************************/

    /**
     * Return a randomly generated password.
     *
     * Note :
     * a random password is useful to avoid creating users with empty password fields,
     * which could be a security issue if the user authentication method may be reset
     * to 'platform'
     *
     * @param int $length Number of characters for the password
     * @return string New random password
     */
    protected function generate_random_password($length = 8)
    {
        $password = '';
        $possible = '0123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

        $index = 0;
        while ($index < $length)
        {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $password .= $char;
            $index ++;
        }

        return $password;
    }

    /**
     * Create a session for the user and redirect to the Chamilo homepage
     *
     * @param User $user
     * @param bool $redirect_to_homepage Indicates if the user must be redirected to the homepage
     */
    protected function login($user, $redirect_to_homepage = true)
    {
        if (is_a($user, self :: USER_OBJECT_CLASSNAME))
        {
            Session :: register('_uid', $user->get_id());

            if ($redirect_to_homepage)
            {
                $this->redirect_to_home_page();
            }
        }
    }

    /*
     * Redirects to the Chamilo homepage
     */
    protected function redirect_to_home_page()
    {
        //header('Location: ' . Configuration :: get_instance()->get_parameter('general', 'root_web'));
        header('Location: ' . Path :: get(WEB_PATH));
        exit();
    }

    /**
     * Redirects to the right request page
     *
     * @param bool $as_new_user Indicates wether the text on the request form page must be formatted for newly created user
     */
    protected function redirect_to_rights_request_form($as_new_user = true)
    {
        $link_params = array('go' => 'request_rights');
        if ($as_new_user)
        {
            $link_params[RightsManagerRoleRequesterComponent :: PARAM_IS_NEW_USER] = '1';
        }

        //header('Location: ' . Configuration :: get_instance()->get_parameter('general', 'root_web') . '/' . Redirect :: get_link('rights', $link_params, null, false, Redirect :: TYPE_CORE));
        header('Location: ' . Path :: get(WEB_PATH). '/' . Redirect :: get_link('rights', $link_params, null, false, Redirect :: TYPE_CORE));
        exit();
    }

    /**
     * Translate a given language code into a string that can be stored in
     * the user 'language' field.
     *
     * Example: 'fr-ch' --> 'french'
     *
     * TODO: move the method in common Chamilo code
     * 		 i.e. in /common/translation/translation.class.php ?
     *
     * @param string $lang
     * @return string Language that can be stored in the user 'language' field
     */
    protected function get_language_code($lang)
    {
        if (! isset($lang) || strlen($lang) == 0)
        {
            return PlatformSetting :: get_instance()->get('platform_language');
        }

        if (substr($lang, 0, 3) == 'fr-')
        {
            $lang = 'fr';
        }
        else
            if (substr($lang, 0, 3) == 'en-')
            {
                $lang = 'en';
            }
            else
                if (substr($lang, 0, 3) == 'nl-')
                {
                    $lang = 'nl';
                }
                else
                    if (substr($lang, 0, 3) == 'es-')
                    {
                        $lang = 'es';
                    }

        switch ($lang)
        {
            case 'fr' :
                $lang = self :: FRENCH;
                break;

            case 'en' :
                $lang = self :: ENGLISH;
                break;

            case 'nl' :
                $lang = self :: DUTCH;
                break;

            case 'es' :
                $lang = self :: SPANISH;
                break;

            default :

                return PlatformSetting :: get_instance()->get('platform_language');
                break;
        }

        /*
         * Check that the language folder exists before returning it.
         * If not, return the default platform language
         */
        if (file_exists(Path :: get_language_path() . $lang))
        {
            return $lang;
        }
        else
        {
            return PlatformSetting :: get_instance()->get('platform_language');
        }
    }

    /**
     * Creates a username based on the firstname and the lastname of the user
     * If such a username already exists, a number is added at the end of the username
     * until the username is available
     *
     * @param string $firstname
     * @param string $lastname
     * @return string an avalaible username
     */
    function generate_username($firstname, $lastname)
    {
        $special_chars = array(' ', '\'', '-');
        $firstname = strtolower(str_replace($special_chars, '', $firstname));
        $lastname = strtolower(str_replace($special_chars, '', $lastname));

        $username = $lastname . substr($firstname, 0, 1);
        return $this->complete_username_until_available($username, null);
    }

    /**
     * recursive function to generate an available username
     *
     * @param string $username The username without suffix
     * @param int $suffix_number The suffix used to make the username unique
     * @return string An avalaible username
     */
    private function complete_username_until_available($username, $suffix_number = null)
    {
        $udm = UserDataManager :: get_instance();

        if (isset($suffix_number))
        {
            $user = $udm->retrieve_user_by_username($username . $suffix_number);
        }
        else
        {
            $user = $udm->retrieve_user_by_username($username);
        }

        if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME))
        {
            $suffix_number = isset($suffix_number) ? ++ $suffix_number : 2;
            return $this->complete_username_until_available($username, $suffix_number);
        }
        else
        {
            return isset($suffix_number) ? $username . $suffix_number : $username;
        }
    }

    /**
     * Returns an existing user from the datasource by searching on his external uid.
     * Returns null if no user corresponds to the given external uid.
     *
     * @param string $external_uid
     * @return User
     */
    protected function retrieve_user_by_external_uid($external_uid)
    {
        if (isset($external_uid) && strlen($external_uid) > 0)
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user_by_external_uid($external_uid);

            if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME))
            {
                return $user;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    function get_password_requirements()
    {
        return null;
    }

    /**
     * Always returns false as the user's password
     * is not stored in the Chamilo datasource.
     *
     * @return bool false
     */
    function change_password($user, $old_password, $new_password)
    {
    	return false;
    }
}
?>