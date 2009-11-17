<?php
/**
 * $Id: webservices_group.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.webservices
 */
/*
 * This is the class which contains all the webservices for the Group application.
 * Each webservice checks first the provided hash (which resides in the 'hash' field of every input stream), to see if a call may be made.
 * Next the input is passed to a validator object. This validator validates the input and, if necessary, retrieves ID's based on names.
 * E.g. a userid/groupid based on the provided username/groupname. This is because the ID's of objects are never public knowledge so it's for example impossible
 * for an outsider to delete a user based on the ID of said user. Not in one go anyway.
 * The expected input/output of these webservices goes as follows:
 *
 * get_group:
 *  -input: A Group object with the property 'name' filled in.
 *  -output: The full corresponding Group object with all the available properties filled in.
 *
 * get_groups:
 *  -input: An array of Group objects with for each the property 'name' filled in.
 *  -output: Array of Group objects of the requested groups.
 *
 * delete_group:
 *  -input: A Group object with the property 'name' filled in.
 *  -output: Nothing.
 *
 * delete_groups:
 *  -input: An array of Group objects with for each the property 'name' filled in.
 *  -output: Nothing.
 *
 * create_group:
 *  -input: A Group object with all the required properties filled in.
 *  -output: Nothing.
 *
 * create_groups:
 *  -input: An array of Group objects with all the required properties filled in.
 *  -output: Nothing.
 *
 * update_group:
 *  -input: A Group object with all the required properties filled in.
 *  -output: Nothing.
 *
 * update_groups:
 *  -input: A Group object with all the required properties filled in.
 *  -output: Nothing.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
require_once (dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebServicesGroup();
$handler->run();

class WebServicesGroup
{
    private $webservice;
    private $functions;
    private $validator;

    function WebServicesGroup()
    {
        $this->webservice = Webservice :: factory($this);
        $this->validator = Validator :: get_validator('group');
    }

    function run()
    {
        $functions = array();
        
        $functions['get_group'] = array('input' => new Group(), 'output' => new Group());
        
        $functions['get_groups'] = array('array_input' => true, 'input' => array(new Group()), 'output' => array(new Group()), 'array_output' => true);
        
        $functions['create_group'] = array('input' => new Group());
        
        $functions['create_groups'] = array('array_input' => true, 'input' => array(new Group()));
        
        $functions['update_group'] = array('input' => new Group());
        
        $functions['update_groups'] = array('array_input' => true, 'input' => array(new Group()));
        
        $functions['delete_group'] = array('input' => new Group());
        
        $functions['delete_groups'] = array('array_input' => true, 'input' => array(new Group()));
        
        $functions['subscribe_user'] = array('input' => new GroupRelUser());
        
        $functions['subscribe_users'] = array('array_input' => true, 'input' => array(new GroupRelUser()));
        
        $functions['unsubscribe_user'] = array('input' => new GroupRelUser());
        
        $functions['unsubscribe_users'] = array('array_input' => true, 'input' => array(new GroupRelUser()));
        
        $this->webservice->provide_webservice($functions);
    
    }

    function get_group(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'get group'))
        {
            $gdm = GroupDataManager :: get_instance();
            if ($this->validator->validate_retrieve($input_group[input]))
            {
                $group = $gdm->retrieve_group_by_name($input_group[input][name]);
                if (! empty($group))
                {
                    return $group->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error(Translation :: get('Group') . ' ' . $input_group[input][name] . ' ' . Translation :: get('wasNotFoundInTheDatabase') . '.');
                }
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function get_groups(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'get groups'))
        {
            $gdm = DatabaseGroupDataManager :: get_instance();
            foreach ($input_group[input] as $group)
            {
                if ($this->validator->validate_retrieve($group))
                {
                    $g = $gdm->retrieve_group_by_name($group[name]);
                    if (! empty($g))
                        $groups[] = $g->get_default_properties();
                    else
                    {
                        return $this->webservice->raise_error(Translation :: get('Group') . ' ' . $group[name] . ' ' . Translation :: get('wasNotFoundInTheDatabase') . '.');
                    }
                
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $groups;
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function create_group(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'create group'))
        {
            if ($this->validator->validate_create($input_group[input]))
            {
                $g = new Group();
                $g->set_default_properties($input_group[input]);
                return $this->webservice->raise_message($g->create());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function create_groups(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'create groups'))
        {
            foreach ($input_group[input] as $group)
            {
                if ($this->validator->validate_create($group))
                {
                    $g = new Group();
                    $g->set_default_properties($input_group[input]);
                    $g->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('GroupsCreated'));
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function update_group($input_group)
    {
        if ($this->webservice->can_execute($input_group, 'update group'))
        {
            if ($this->validator->validate_update($input_group[input]))
            {
                $g = new Group();
                $g->set_default_properties($input_group[input]);
                return $this->webservice->raise_message($g->update());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function update_groups($input_group)
    {
        if ($this->webservice->can_execute($input_group, 'update groups'))
        {
            foreach ($input_group[input] as $group)
            {
                if ($this->validator->validate_update($group))
                {
                    $g = new Group();
                    $g->set_default_properties($input_group[input]);
                    $g->update();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('GroupsUpdated'));
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function delete_group(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'delete group'))
        {
            if ($this->validator->validate_delete($input_group[input]))
            {
                $g = new Group();
                $g->set_default_properties($input_group[input]);
                return $this->webservice->raise_message($g->delete());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function delete_groups(&$input_group)
    {
        if ($this->webservice->can_execute($input_group, 'delete groups'))
        {
            foreach ($input_group[input] as $group)
            {
                if ($this->validator->validate_delete($group))
                {
                    $g = new Group();
                    $g->set_default_properties($input_group[input]);
                    $g->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('GroupsDeleted'));
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function subscribe_user(&$input_group_rel_user)
    {
        if ($this->webservice->can_execute($input_group_rel_user, 'subscribe user'))
        {
            if ($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user[input]))
            {
                $gru = new GroupRelUser($input_group_rel_user[input][group_id], $input_group_rel_user[input][user_id]);
                return $this->webservice->raise_message($gru->create());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function subscribe_users(&$input_group_rel_user)
    {
        if ($this->webservice->can_execute($input_group_rel_user, 'subscribe users'))
        {
            foreach ($input_group_rel_user[input] as $group_rel_user)
            {
                if ($this->validator->validate_subscribe_or_unsubscribe($group_rel_user))
                {
                    $gru = new GroupRelUser($group_rel_user[group_id], $group_rel_user[user_id]);
                    $gru->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersSubscribed'));
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function unsubscribe_user(&$input_group_rel_user)
    {
        if ($this->webservice->can_execute($input_group_rel_user, 'unsubscribe user'))
        {
            if ($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user[input]))
            {
                $gru = new GroupRelUser($input_group_rel_user[input][group_id], $input_group_rel_user[input][user_id]);
                return $this->webservice->raise_message($gru->delete());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

    function unsubscribe_users(&$input_group_rel_user)
    {
        if ($this->webservice->can_execute($input_group_rel_user, 'unsubscribe users'))
        {
            foreach ($input_group_rel_user[input] as $group_rel_user)
            {
                if ($this->validator->validate_subscribe_or_unsubscribe($group_rel_user))
                {
                    $gru = new GroupRelUser($group_rel_user[group_id], $group_rel_user[user_id]);
                    $gru->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersUnsubscribed'));
        }
        else
        {
            return $this->webservice->get_message();
        }
    }

}