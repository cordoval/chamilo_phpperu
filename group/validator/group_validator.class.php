<?php
/**
 * $Id: group_validator.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.validator
 */

/*
 * The purpose of this class is to validate the given Group-properties:
 * -To check if all the required properties are there and, in some cases, have valid contents
 * -To check if e.g. the name of a person or group exists and retrieve the respective ID where necessary
 * Each validator also generates an error message if something goes wrong,
 * together with an error source to keep track of what was happening when something went wrong.
 * This is especially useful during large batch assignments, so you can easily see which entry produces errors.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
class GroupValidator extends Validator
{
    private $gdm;
    private $udm;

    function GroupValidator()
    {
        $this->gdm = GroupDataManager :: get_instance();
        $this->udm = UserDataManager :: get_instance();
    }

    private function get_required_group_property_names()
    {
        return array(Group :: PROPERTY_NAME, Group :: PROPERTY_SORT, Group :: PROPERTY_PARENT, Group :: PROPERTY_LEFT_VALUE, Group :: PROPERTY_RIGHT_VALUE);
    }

    private function get_required_group_rel_user_property_names()
    {
        return array(GroupRelUser :: PROPERTY_GROUP_ID, GroupRelUser :: PROPERTY_USER_ID);
    }

    function validate_retrieve(&$groupProperties)
    {
        $this->errorSource = Translation :: get('ErrorRetrievingGroup');
        
        if ($groupProperties[name] == null)
        {
            $this->errorMessage = Translation :: get('GroupnameIsRequired');
            return false;
        }
        
        return true;
    }

    function validate_create(&$groupProperties)
    {
        $this->errorSource = Translation :: get('ErrorCreatingGroup') . ': ' . $groupProperties[Group :: PROPERTY_NAME];
        
        if (! $this->validate_properties($groupProperties, $this->get_required_group_property_names()))
            return false;
        
        if (! $this->validate_property_names($groupProperties, Group :: get_default_property_names()))
            return false;
        
        if (! $this->gdm->is_groupname_available($groupProperties[Group :: PROPERTY_NAME]))
        {
            $this->errorMessage = Translation :: get('GroupnameIsAlreadyUsed');
            return false;
        }
        
        /*
         * If the ID of the parent is 0, it's a root group and thus has no parent.
         */
        
        if ($groupProperties[Group :: PROPERTY_PARENT] != '0')
        {
            $var = $this->get_group_id($groupProperties[Group :: PROPERTY_PARENT]);
            if (! $var)
            {
                $this->errorMessage = Translation :: get('ParentGroupName') . ' ' . $groupProperties[Group :: PROPERTY_PARENT] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
                return false;
            }
            else
                $groupProperties[Group :: PROPERTY_PARENT] = $var;
        }
        
        return true;
    }

    function validate_update(&$groupProperties)
    {
        $this->errorSource = Translation :: get('ErrorUpdatingGroup') . ': ' . $groupProperties[Group :: PROPERTY_NAME];
        
        if (! $this->validate_properties($groupProperties, $this->get_required_group_property_names()))
            return false;
        
        if (! $this->validate_property_names($groupProperties, Group :: get_default_property_names()))
            return false;
        
        $var = $this->get_group_id($groupProperties[Group :: PROPERTY_NAME]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('Group') . ' ' . $groupProperties[Group :: PROPERTY_NAME] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $groupProperties[Group :: PROPERTY_ID] = $var;
        
        if ($groupProperties[Group :: PROPERTY_PARENT] != '0')
        {
            $var = $this->get_group_id($groupProperties[Group :: PROPERTY_PARENT]);
            if (! $var)
            {
                $this->errorMessage = Translation :: get('ParentGroupName') . ' ' . $groupProperties[Group :: PROPERTY_PARENT] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
                return false;
            }
            else
                $groupProperties[Group :: PROPERTY_PARENT] = $var;
        }
        return true;
    }

    function validate_delete(&$groupProperties)
    {
        $this->errorSource = Translation :: get('ErrorDeletingGroup') . ': ' . $groupProperties[Group :: PROPERTY_NAME];
        
        if (! $this->validate_properties($groupProperties, $this->get_required_group_property_names()))
            return false;
        
        if (! $this->validate_property_names($groupProperties, Group :: get_default_property_names()))
            return false;
        
        $var = $this->get_group_id($groupProperties[Group :: PROPERTY_NAME]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('Group') . ' ' . $groupProperties[Group :: PROPERTY_NAME] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $groupProperties[Group :: PROPERTY_ID] = $var;
        
        return true;
    }

    function validate_subscribe_or_unsubscribe(&$input_group_rel_user)
    {
        $this->errorSource = Translation :: get('ErrorSubscribingOrUnsubscribingUser') . ' ' . $input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID] . ' ' . Translation :: get('ToFrom') . ' ' . Translation :: get('Group') . ' ' . $input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID];
        
        if (! $this->validate_properties($input_group_rel_user, $this->get_required_group_rel_user_property_names()))
            return false;
        
        if (! $this->validate_property_names($input_group_rel_user, GroupRelUser :: get_default_property_names()))
            return false;
        
        $var = $this->get_person_id($input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('User') . ' ' . $input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID] = $var;
        
        $var = $this->get_group_id($input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID]);
        if (! $var)
        {
            $this->errorMessage = Translation :: get('Group') . ' ' . $input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID] . ' ' . Translation :: get('wasNotFoundInTheDatabase');
            return false;
        }
        else
            $input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID] = $var;
        
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

    private function get_group_id($group_name)
    {
        $group = $this->gdm->retrieve_group_by_name($group_name);
        if (isset($group) && count($group->get_default_properties()) > 0)
        {
            return $group->get_id();
        }
        else
        {
            return false;
        }
    }

    private function does_group_exist($group_id)
    {
        return $this->gdm->count_groups(new EqualityCondition(Group :: PROPERTY_ID, $group_id)) != 0;
    }

    private function does_user_exist($user_id)
    {
        return $this->udm->count_users(new EqualityCondition(User :: PROPERTY_USER_ID, $user_id)) != 0;
    }
}
?>