<?php
/*
 * $Id: webservices_user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.webservices
 *
 * This is the class which contains all the webservices for the User application.
 * Each webservice checks first the provided hash (which resides in the 'hash' field of every input stream), to see if a call may be made.
 * Next the input is passed to a validator object. This validator validates the input and, if necessary, retrieves ID's based on names.
 * E.g. a userid based on the provided username. This is because the ID's of objects are never public knowledge so it's for example impossible
 * for an outsider to delete a user based on the ID of said user. Not in one go anyway.
 * The expected input/output of these webservices goes as follows:
 *
 * get_user:
 *  -input: A User object with the property 'username' filled in.
 *  -output: The full corresponding User object with all the available properties filled in.
 *
 * get_all_users:
 *  -input: Nothing.
 *  -output: Array of User objects of all the available users.
 *
 * delete_user:
 *  -input: A User object with the property 'username' filled in.
 *  -output: Nothing.
 *
 * delete_users:
 *  -input: An array of User objects with for each the property 'username' filled in.
 *  -output: Nothing.
 *
 * create_user:
 *  -input: A User object with all the required properties filled in.
 *  -output: Nothing.
 *
 * create_users:
 *  -input: An array of User objects with all the required properties filled in.
 *  -output: Nothing.
 *
 * update_user:
 *  -input: A User object with all the required properties filled in.
 *  -output: Nothing.
 *
 * update_users:
 *  -input: A User object with all the required properties filled in.
 *  -output: Nothing.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */

require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebServicesUser();
$handler->run();

class WebServicesUser
{
    private $webservice;
    private $validator;

    function WebServicesUser()
    {
        $this->webservice = Webservice :: factory($this);
        $this->validator = Validator :: get_validator('user');
    }

    function run()
    {
        
        $functions = array();
        
        $functions['get_user'] = array('input' => new User(), 'output' => new User());
        
        $functions['get_all_users'] = array('array_output' => true, 'output' => array(new User()));
        
        $functions['delete_user'] = array('input' => new User());
        
        $functions['create_user'] = array('input' => new User());
        
        $functions['create_users'] = array('array_input' => true, 'input' => array(new User()));
        
        $functions['update_user'] = array('input' => new User());
        
        $functions['delete_users'] = array('array_input' => true, 'input' => array(new User()));
        
        $functions['update_users'] = array('array_input' => true, 'input' => array(new User()));
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        $this->webservice->provide_webservice($functions);
    }

    function get_user($input_user)
    {
        if ($this->webservice->can_execute($input_user, 'get user'))
        {
            $udm = UserDataManager :: get_instance();
            if ($this->validator->validate_retrieve($input_user[input])) //input validation
            {
                $user = $udm->retrieve_user_by_username($input_user[input][username]);
                if (! empty($user))
                {
                    return $user->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error(Translation :: get('User') . ' ' . $input_user[input][username] . ' ' . Translation :: get('NotFound') . '.', Translation :: get('Client'), Translation :: get('NotFound'), Translation :: get('UserDoesNotExistInTheDatabase'));
                }
            }
            else
            {
                return $this->webservice->raise_error(Translation :: get('CouldNotRetrieveUser') . $input_user[input][username] . '. ' . Translation :: get('PleaseCheckTheDataYou\'veProvided') . '.');
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    
    }

    function get_all_users($input_user)
    {
        if ($this->webservice->can_execute($input_user, 'get all users'))
        {
            $udm = UserDataManager :: get_instance();
            $users = $udm->retrieve_users();
            $users = $users->as_array();
            foreach ($users as &$user)
            {
                $user = $user->get_default_properties();
            }
            return $users;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function delete_user(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'delete user'))
        {
            if ($this->validator->validate_delete($input_user[input]))
            {
                $u = new User(0, $input_user[input]);
                return $this->webservice->raise_message($u->delete());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function create_user(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'create user'))
        {
            if ($this->validator->validate_create($input_user[input]))
            {
                $u = new User(0, $input_user[input]);
                return $this->webservice->raise_message($u->create());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function update_user(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'update user'))
        {
            if ($this->validator->validate_update($input_user[input]))
            {
                $u = new User(0, $input_user[input]);
                return $this->webservice->raise_message($u->update());
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function create_users(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'create users'))
        {
            foreach ($input_user[input] as $user)
            {
                if ($this->validator->validate_create($user))
                {
                    $u = new User(0, $user);
                    $u->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersCreated') . '.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function update_users(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'update users'))
        {
            foreach ($input_user[input] as $user)
            {
                if ($this->validator->validate_update($user))
                {
                    $u = new User(0, $user);
                    $u->update();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersUpdated') . '.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }

    function delete_users(&$input_user)
    {
        if ($this->webservice->can_execute($input_user, 'delete users'))
        {
            foreach ($input_user[input] as $user)
            {
                if ($this->validator->validate_delete($user))
                {
                    $u = new User(0, $user);
                    $u->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message(), null, Translation :: get('Client'), $this->validator->get_error_source());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersDeleted') . '.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }
}
?>