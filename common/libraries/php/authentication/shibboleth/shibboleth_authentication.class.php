<?php
/**
 * $Id: shibboleth_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.shibboleth
 */
require_once dirname(__FILE__) . '/../external_authentication.class.php';

/**
 * This class allow to login into Chamilo by using the Shibboleth authentication system.
 * 
 * To use this class, the folder 'shibboleth' must be configured in your webserver settings 
 * to be protected by Shibboleth. In this way, user attributes can be retrieved from the code.
 *
 * Shibboleth attributes can be mapped to Chamilo user atributes by setting the correct mappings
 * in the 'shibboleth.xml' file. Role / Group mapping can be configured in this file as well.
 * 
 * The configuration file allow to define :
 * 
 * 		- the mapping between Shibboleth attributes and Chamilo user attributes
 * 		- the mapping between the 'affiliation' attribute and Chamilo user roles / groups
 * 		- default values for Chamilo user attributes
 * 		- what Chamilo user attributes must be updated when a user logs in again
 * 
 * About the Role / Group mapping
 * *******************************
 * The role / group mapping can retrieved from an attribute mapped to 'affiliation'
 * This attribute may contain more than one value, separated by a character (typically a semi-colon).
 * 
 * The config file allow to add possible values that may be given by Shibboleth and allow for each of them
 * to specify to which group and / or role it must be mapped.
 * If a specific value must be mapped to more than one group or role, you can add many times the same value.
 * 
 * Each value has a precedence. It allows to look for values in priority. All values with the same precedence
 * are searched in the Shibboleth attributes at the same time. If the user 'affiliation' attribute contains one 
 * of the searched values, the role / group links are created and the next values with the next precedence are ignored.
 * If no value match the user 'affiliation' attribute for a precedence, then the values with next precedence 
 * are looked for.
 * 
 * It allows for instance in weblcms to check first if a user is a teacher. If he is a teacher, he can be created 
 * with "teacher" rights, meaning with specific group(s) and / or role(s) links. In the case the user was not recognized
 * as a teacher on the first step (with the first precedence value), the system can then (and only then) check 
 * if the user is a "student", and create the corresponding role / group links for him.    
 * 
 */
class ShibbolethAuthentication extends ExternalAuthentication
{
    const SHIBBOLETH_AUTHENTICATION = 'shibboleth';
    const RIGHTS_SEPARATOR = 'RIGHTS_SEPARATOR';
    
    private $config_xml_document = null;
    private $user_status_is_unclear = false;

    /*
     * Constructor
     */
    function ShibbolethAuthentication()
    {
        $this->initialize();
    }

    /*
     * Initialize the Shibboleth authentication configuration
     */
    protected function initialize()
    {
        $doc = simplexml_load_file('shibboleth.xml');
        if ($doc !== false)
        {
            $this->config_xml_document = $doc;
            
            parent :: initialize();
            
            $this->set_authentication_source_name(self :: SHIBBOLETH_AUTHENTICATION);
            $this->set_automatic_registration(true);
            $this->set_register_user_without_role(false);
        }
        else
        {
            echo 'unable to load configuration';
        }
    }

    /**
     * Inherited. Note that with Shibboleth $user, $username and $password are not used
     *
     * @param unknown_type $user
     * @param unknown_type $username
     * @param unknown_type $password
     */
    function check_login($user, $username, $password = null)
    {
        /*
         * With Shibboleth, we don't use $user, $username and $password
         */
        unset($user);
        unset($username);
        unset($password);
        
        /*
         * Check if user already exists
         */
        $is_new_user = false;
        $user = $this->get_existing_user();
        
        if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME))
        {
            //user already exists
            

            if ($this->has_fields_to_update_at_login())
            {
                /*
                 * Update user's fields that must be updated when an existing user logs in 
                 */
                
                $user = $this->set_user_attributes($user, $this->get_fields_to_update_at_login());
                
                //save user in datasource
                if (! $user->update())
                {
                    echo 'An error occured while updating your informations';
                }
                else
                {
                    if (in_array(self :: PARAM_MAPPING_AFFILIATION, $this->get_fields_to_update_at_login()))
                    {
                        if (! $this->set_user_rights($user))
                        {
                            echo 'Unable to set the user rights';
                        }
                    }
                }
            }
        }
        else
        {
            //user does not exist yet
            

            $is_new_user = true;
            
            if ($this->is_automatic_registration_enabled())
            {
                $user = $this->get_new_user();
                
                if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME))
                {
                    if ($user->create())
                    {
                        if (array_key_exists(self :: PARAM_MAPPING_AFFILIATION, $this->get_user_attribute_mapping()))
                        {
                            if (! $this->set_user_rights($user))
                            {
                                echo 'Unable to set the user rights';
                            }
                        }
                    }
                }
            }
            else
            {
                echo 'You don\'t have the right to access this platform. Please contact the platform administrator';
            }
        }
        
        /*
         * Login user
         */
        if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME))
        {
            if ($this->user_status_is_unclear && $this->get_display_rights_form_when_unclear_status())
            {
                $this->login($user, false);
                $this->redirect_to_rights_request_form($is_new_user);
            }
            else
            {
                $this->login($user, true);
            }
        }
        else
        {
            echo 'Unable to perform login';
        }
    }

    /**
     * override
     */
    protected function initialize_user_attributes_mapping()
    {
        if (isset($this->config_xml_document))
        {
            $attributes_mapping = $this->config_xml_document->xpath('/authentication/attribute_mapping/attribute');
            
            $user_attribute_mapping = array();
            $fields_to_update_at_login = array();
            
            foreach ($attributes_mapping as $attribute)
            {
                $user_attribute_mapping[(string) $attribute->attributes()->mapped_to] = array('name' => (string) $attribute->attributes()->name, 'default' => (string) $attribute->attributes()->default_value);
                
                if (isset($attribute->attributes()->update_at_login) && $attribute->attributes()->update_at_login == 'true')
                {
                    $fields_to_update_at_login[] = (string) $attribute->attributes()->mapped_to;
                }
            }
            
            $this->set_user_attribute_mapping($user_attribute_mapping);
            $this->set_fields_to_update_at_login($fields_to_update_at_login);
        }
        else
        {
            echo '<p>Attribute mapping initialization error</p>';
        }
    }

    /**
     * override
     */
    protected function initialize_fields_to_update_at_login()
    {
        /*
         * Nothing is implemented here, as the initialization is already done when 
         * 'initialize_user_attributes_mapping()' is called;
         */
    }

    /**
     * override
     */
    protected function initialize_role_attributes_mapping()
    {
        if (isset($this->config_xml_document))
        {
            $separator = $this->config_xml_document->xpath('/authentication/role_mapping/@name_separator');
            $this->set_config_parameter(self :: RIGHTS_SEPARATOR, $separator[0]);
            
            $display_form = $this->config_xml_document->xpath('/authentication/role_mapping/@display_request_form_when_unclear');
            if (isset($display_form[0]) && $display_form[0] == 'true')
            {
                $this->set_display_rights_form_when_unclear_status(true);
            }
            else
            {
                $this->set_display_rights_form_when_unclear_status(false);
            }
            
            $role_mapping = array();
            
            $value_nodes = $this->config_xml_document->xpath('/authentication/role_mapping/value');
            
            foreach ($value_nodes as $value_node)
            {
                $name = (string) $value_node->attributes()->name;
                $precedence = (string) $value_node->attributes()->precedence;
                $group_id = (string) $value_node->attributes()->group_id;
                $role_id = (string) $value_node->attributes()->role_id;
                
                if (! array_key_exists($precedence, $role_mapping))
                {
                    $role_mapping[$precedence] = array();
                }
                
                if (! array_key_exists($name, $role_mapping[$precedence]))
                {
                    $role_mapping[$precedence][$name] = array();
                    $role_mapping[$precedence][$name]['group_id'] = array();
                    $role_mapping[$precedence][$name]['role_id'] = array();
                }
                
                if (isset($group_id) && strlen($group_id) > 0)
                {
                    $role_mapping[$precedence][$name]['group_id'][] = $group_id;
                }
                if (isset($role_id) && strlen($role_id) > 0)
                {
                    $role_mapping[$precedence][$name]['role_id'][] = $role_id;
                }
            }
            
            $this->set_user_role_attribute_mapping($role_mapping);
        }
        else
        {
            echo '<p>Role mapping initialization error</p>';
        }
    }

    /**
     * Returns the current user logged through Shibboleth if he already
     * exists in the datasource. Otherwise return null.
     *
     * @return User The current user if he already exists in the datasource 
     */
    protected function get_existing_user()
    {
        $user_mapping = $this->get_user_attribute_mapping();
        $shibboleth_id = $this->get_shibboleth_value($user_mapping[self :: PARAM_MAPPING_EXTERNAL_UID]['name']);
        
        $user = $this->retrieve_user_by_external_uid($shibboleth_id);
        
        if (isset($user))
        {
            return $user;
        }
        else
        {
            return null;
        }
    }

    /**
     * Creates and returns a new User based on the Shibboleth attributes
     *
     * @return User A new user
     */
    protected function get_new_user()
    {
        $user = new User();
        $user = $this->set_user_attributes($user);
        return $user;
    }

    /**
     * override
     *
     * @param User $user
     * @param array $fields_to_set
     * @return User
     */
    protected function set_user_attributes($user, $fields_to_set = null)
    {
        foreach ($this->get_user_attribute_mapping() as $field_name => $field_source)
        {
            $must_set = true;
            if (isset($fields_to_set) && is_array($fields_to_set) && count($fields_to_set) > 0 && ! in_array($field_name, $fields_to_set))
            {
                $must_set = false;
            }
            
            if ($must_set)
            {
                $value = $this->get_shibboleth_value($field_source['name']);
                
                /*
        	     * Default value if no value is found
        	     */
                if (! isset($value))
                {
                    if (isset($field_source['default']) && strlen($field_source['default']) > 0)
                    {
                        $value = $field_source['default'];
                    }
                }
                
                if ($field_name == self :: PARAM_MAPPING_LANG)
                {
                    $lang = $this->get_language_code($value);
                    $user->set_language($lang);
                }
                elseif (method_exists($user, 'set_' . $field_name))
                {
                    call_user_func_array(array($user, 'set_' . $field_name), array($value));
                }
            }
        }
        
        /*
         * Default mandatory values if no mapping is defined
         */
//        $user_lang = $user->get_language();
//        if (! isset($user_lang))
//        {
//            $lang = $this->get_language_code(null);
//            $user->set_language($lang);
//        }
        
        /*
         * case of new user -> init some more default values
         */
        $user_id = $user->get_id();
        if (! isset($user_id))
        {
            $user->set_username($this->generate_username($user->get_firstname(), $user->get_lastname()));
            $user->set_password(md5($this->generate_random_password())); //generate random password to prevent security hole if the user's auth_source is modified
            $user->set_auth_source($this->get_authentication_source_name());
            $user->set_platformadmin(0);
        }
        
        return $user;
    }

    /**
     * Add the given user in groups and / or grant the user roles.
     * The user must already have an id (as it is used to create groups and /or role links)
     *
     * @param User $user
     * @return Boolean role(s) / group(s) affiliation result
     */
    protected function set_user_rights($user)
    {
        if ($this->has_user_role_attribute_mapping() && array_key_exists(self :: PARAM_MAPPING_AFFILIATION, $this->get_user_attribute_mapping()))
        {
            $user_id = $user->get_id();
            if (isset($user) && is_a($user, self :: USER_OBJECT_CLASSNAME) && isset($user_id))
            {
                /*
                 * Deletes all user_role and user_group relations for the user 
                 * before recreating them with his affiliation attribute
                 */
                //$user_roles = $user->get_roles();
                $user_groups = $user->get_user_groups();
                
                if (isset($user_groups))
                {
                    while ($user_group = $user_groups->next_result())
                    {
                        if (! $user_group->delete())
                        {
                            echo 'Unable to clear user groups';
                        }
                    }
                }
                
                //                if (isset($user_roles))
                //                {
                //                    while ($user_role = $user_roles->next_result())
                //                    {
                //                        if (! $user_role->delete())
                //                        {
                //                            echo 'Unable to clear user roles';
                //                        }
                //                    }
                //                }
                

                $user_mapping = $this->get_user_attribute_mapping();
                $affiliation = $this->get_shibboleth_value($user_mapping[self :: PARAM_MAPPING_AFFILIATION]['name']);
                
                /*
                 * Default value if no value is found
                 */
                if (! isset($affiliation))
                {
                    if (isset($user_mapping[self :: PARAM_MAPPING_AFFILIATION]['default']) && strlen($user_mapping[self :: PARAM_MAPPING_AFFILIATION]['default']) > 0)
                    {
                        $affiliation = $user_mapping[self :: PARAM_MAPPING_AFFILIATION]['default'];
                    }
                }
                
                $affiliation_values = explode($this->get_config_parameter(self :: RIGHTS_SEPARATOR), $affiliation);
                
                $role_mapping = $this->get_user_role_attribute_mapping();
                
                /*
                 * If the user has an affiliation value that the config file doesn't know,
                 * his status is considered as unclear
                 */
                $configured_affiliation_values = array();
                
                foreach ($role_mapping as $mapping_for_precedence)
                {
                    foreach ($mapping_for_precedence as $attribute_value => $rights)
                    {
                        $configured_affiliation_values[] = $attribute_value;
                    }
                }
                foreach ($affiliation_values as $affiliation_value)
                {
                    if (! in_array($affiliation_value, $configured_affiliation_values))
                    {
                        $this->user_status_is_unclear = true;
                    }
                }
                
                /*
                 * Loops on each level of precedence and checks if a corresponding
                 * user affiliation value is found.
                 * 
                 * If a value is found in the user attributes, the link between 
                 * the user and group(s) and / or role(s) are created. The process then stops,
                 * meaning we don't look for other precedence levels
                 * 
                 */
                $mapping_found = false;
                foreach ($role_mapping as $mapping_for_precedence)
                {
                    if ($mapping_found)
                    {
                        break;
                    }
                    
                    $groups = array();
                    //                    $roles = array();
                    

                    foreach ($mapping_for_precedence as $attribute_value => $rights)
                    {
                        if (in_array($attribute_value, $affiliation_values))
                        {
                            /*
                    	     * Find group mappings
                    	     */
                            foreach ($rights['group_id'] as $group_id)
                            {
                                if (! in_array($group_id, $groups))
                                {
                                    $groups[] = $group_id;
                                }
                            }
                            
                            //                            /*
                            //                    	     * Find role mappings
                            //                    	     */
                            //                            foreach ($rights['role_id'] as $role_id)
                            //                            {
                            //                                if (! in_array($role_id, $roles))
                            //                                {
                            //                                    $roles[] = $role_id;
                            //                                }
                            //                            }
                            

                            $mapping_found = true;
                        }
                    }
                }
                
                if (count($groups) > 0)
                {
                    /*
                     * Create group_user records
                     */
                    foreach ($groups as $group_id)
                    {
                        $group_user = new GroupRelUser();
                        $group_user->set_user_id($user_id);
                        $group_user->set_group_id($group_id);
                        
                        if (! $group_user->create())
                        {
                            echo 'Unable to add user in group ' . $group_id;
                        }
                    }
                }
                
                //                if (count($roles) > 0)
                //                {
                //                    /*
                //                     * Create user_role records
                //                     */
                //                    foreach ($roles as $role_id)
                //                    {
                //                        if (! $user->add_role_link($role_id))
                //                        {
                //                            echo 'Unable to grant role ' . $role_id . ' to the user';
                //                        }
                //                    }
                //                }
                

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Return the shibboleth attribute value for the given attribute name
     * 
     * So far Shibboleth gives values to PHP through HTTP headers (Shibboleth 1.x) or 
     * environment variables (Shibboleth 2) that can be read through the $_SERVER variable
     * 
     * @param String $shibboleth_param_name the shibboleth attribute name
     * @return String the shibboleth attribute value
     */
    protected function get_shibboleth_value($shibboleth_param_name)
    {
        $request = new Request();
        return $request->server($shibboleth_param_name);
    }

    /**
     * Print a table in the page with the current Shibboleth attributes.
     * Mainly useful for debugging the Shibboleth system.
     * 
     * Note: see file 'show_my_infos.php' to see how this method can be called
     *
     */
    function print_shibboleth_attributes()
    {
        echo '<table cellpadding="5" cellspacing="0">';
        echo '<tr>';
        echo '<th>User property</th><th></th><th>Shibboleth parameter name</th><th></th><th>User value</th><th></th><th>Default value</th>';
        echo '</tr>';
        
        foreach ($this->get_user_attribute_mapping() as $field_name => $field_source)
        {
            
            echo '<tr>';
            echo '  <td>' . $field_name . '</td>';
            echo '  <td>&rarr;</td>';
            echo '  <td>' . $field_source['name'] . '</td>';
            echo '  <td>&rarr;</td>';
            echo '  <td>' . $this->get_shibboleth_value($field_source['name']) . '</td>';
            echo '  <td>&rarr;</td>';
            echo '  <td>' . $this->get_shibboleth_value($field_source['default']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
}

?>