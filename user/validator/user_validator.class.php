<?php

/**
 * $Id: user_validator.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.validator
 * 
 * The purpose of this class is to validate the given User-properties:
 * -To check if all the required properties are there and, in some cases, have valid contents
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * Each validator also generates an error message if something goes wrong,
 * together with an error source to keep track of what was happening when something went wrong.
 * This is especially useful during large batch assignments, so you can easily see which entry produces errors.
 * 
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
class UserValidator extends Validator
{
    private $udm;

    function UserValidator()
    {
        $this->udm = UserDataManager :: get_instance();
    }

    public function get_required_property_names()
    {
        return array(User :: PROPERTY_USERNAME, User :: PROPERTY_PASSWORD, User :: PROPERTY_AUTH_SOURCE, User :: PROPERTY_STATUS, User :: PROPERTY_DISK_QUOTA, User :: PROPERTY_DATABASE_QUOTA, User :: PROPERTY_VERSION_QUOTA, User :: PROPERTY_ACTIVATION_DATE, User :: PROPERTY_EXPIRATION_DATE, User :: PROPERTY_REGISTRATION_DATE, User :: PROPERTY_ACTIVE);
    }

    function validate_retrieve(&$userProperties)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingUser');
        
        if ($userProperties[username] == null)
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired');
            return false;
        }
        
        return true;
    }

    function validate_create(&$userProperties)
    {
        $this->errorSource = Translation :: get('ErrorCreatingUser') . ': ' . $userProperties[User :: PROPERTY_USERNAME];
        
        if (! $this->validate_properties($userProperties, $this->get_required_property_names()))
            return false;
        
        if (! $this->validate_property_names($userProperties, User :: get_default_property_names()))
            return false;
        
        if (! $this->udm->is_username_available($userProperties[User :: PROPERTY_USERNAME]))
        {
            $this->errorMessage = Translation :: get('UsernameIsAlreadyUsed');
            return false;
        }
        
        if (! $this->check_quota($userProperties))
            return false;
        
        if (! empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if (! $var)
            {
                $this->errorMessage = Translation :: get('Creator') . ' ' . $userProperties[User :: PROPERTY_CREATOR_ID] . ' ' . Translation :: get('doesNotExist');
                return false;
            }
            else
                $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
        }
        
        if ($userProperties[User :: PROPERTY_ACTIVE] !== '0' && $userProperties[User :: PROPERTY_ACTIVE] !== '1' && $userProperties[User :: PROPERTY_ACTIVE] !== false && $userProperties[User :: PROPERTY_ACTIVE] !== true)
        {
            $this->errorMessage = Translation :: get('Property') . ' ' . User :: PROPERTY_ACTIVE . Translation :: get('hasWrongValue') . ': ' . $userProperties[User :: PROPERTY_ACTIVE];
            return false;
        }
        
        return true;
    }

    function validate_update(&$userProperties)
    {
        $this->errorSource = Translation :: get('ErrorUpdatingUser') . ': ' . $userProperties[User :: PROPERTY_USERNAME];
        
        if (! $this->validate_properties($userProperties, $this->get_required_property_names()))
            return false;
        
        if (! $this->validate_property_names($userProperties, User :: get_default_property_names()))
            return false;
            
        /*
         * To look up the username and retrieve the corresponding id
         */
        $var = $this->get_person_id($userProperties[User :: PROPERTY_USERNAME]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('User') . ' ' . $userProperties[User :: PROPERTY_USERNAME] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $userProperties[User :: PROPERTY_USER_ID] = $var;
            
        /*
         * To check if the creator exists and retrieve it's ID.
         */
        
        if (! empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if (! $var)
            {
                $this->errorMessage = Translation :: get('Creator') . ' ' . $userProperties[User :: PROPERTY_CREATOR_ID] . ' ' . Translation :: get('doesNotExist');
                return false;
            }
            else
                $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
        }
        
        if (! $this->check_quota($userProperties))
            return false;
        
        if ($userProperties[User :: PROPERTY_ACTIVE] !== '0' && $userProperties[User :: PROPERTY_ACTIVE] !== '1' && $userProperties[User :: PROPERTY_ACTIVE] !== false && $userProperties[User :: PROPERTY_ACTIVE] !== true)
        {
            $this->errorMessage = Translation :: get('Property') . ' ' . User :: PROPERTY_ACTIVE . Translation :: get('hasWrongValue') . ': ' . $userProperties[User :: PROPERTY_ACTIVE];
            return false;
        }
        
        return true;
    }

    function validate_delete(&$userProperties)
    {
        $this->errorSource = Translation :: get('ErrorDeletingUser') . ': ' . $userProperties[User :: PROPERTY_USERNAME];
        
        if (! $this->validate_property_names($groupProperties, User :: get_default_property_names()))
            return false;
        
        if ($userProperties[username] == null)
        {
            $this->errorMessage = Translation :: get('UsernameIsRequired');
            return false;
        }
        
        /*
         * To look up the username and retrieve the corresponding id
         */
        $var = $this->get_person_id($userProperties[User :: PROPERTY_USERNAME]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('User') . ' ' . $userProperties[User :: PROPERTY_USERNAME] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $userProperties[User :: PROPERTY_USER_ID] = $var;
        
        return true;
    }

    private function get_person_id($person_name)
    {
        $user = $this->udm->retrieve_user_by_username($person_name);
        if (isset($user) && count($user->get_default_properties()) > 0)
        {
            return $user->get_id();
        }
        else
        {
            return false;
        }
    }

    private function does_user_exist($user_id)
    {
        return $this->udm->count_users(new EqualityCondition(User :: PROPERTY_USER_ID, $user_id)) != 0;
    }

    private function check_quota($userProperties)
    {
        if ($userProperties[User :: PROPERTY_DATABASE_QUOTA] < 0)
        {
            $this->errorMessage = User :: PROPERTY_DATABASE_QUOTA . ' ' . Translation :: get('mayNotBeNegative');
            return false;
        }
        
        if ($userProperties[User :: PROPERTY_DISK_QUOTA] < 0)
        {
            $this->errorMessage = User :: PROPERTY_DISK_QUOTA . ' ' . Translation :: get('mayNotBeNegative');
            return false;
        }
        
        if ($userProperties[User :: PROPERTY_VERSION_QUOTA] < 0)
        {
            $this->errorMessage = User :: PROPERTY_VERSION_QUOTA . ' ' . Translation :: get('mayNotBeNegative');
            return false;
        }
        
        return true;
    }

    private function check_dates($userProperties)
    {
        if ($userProperties[User :: PROPERTY_REGISTRATION_DATE] > time())
            return false;
        
        if ($userProperties[User :: PROPERTY_EXPIRATION_DATE] < time() && $userProperties[User :: PROPERTY_EXPIRATION_DATE] != 0)
            return false;
        
        return true;
    }
}
?>