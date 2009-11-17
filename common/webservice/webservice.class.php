<?php
/**
 * $Id: webservice.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package common.webservice
 */
/*
 * This is the abstract webservice class which serves as a backbone for all webservice implementations.
 * The abstract methods call_webservice and provide_webservice need to be implemented in the more specific implementation classes.
 * It also serves as a factory for webservice handlers, which, by default, are of the NuSoap implementation.
 *
 * Furthermore, it serves as a gatekeeper for all webservice calls.
 * The validate_login method will take the input hash,
 * which is actually a hash of (the outside IP address of the client caller + his hashed password (as it is stored in the database)).
 * This constitutes hash 1, so the input hash, should be hash 1. To check if this is so, the validate_login method will capture the IP
 * of the client caller, and look up his hashed password based on the provided username. Hash 1 can then be recalculated and if it indeed
 * matches the input hash, hash 2 will be created, stored in a credential and written to the database.
 * Hash 2 is created as follows: hash(client IP + hash1).
 * Hereafter, hash 3 is sent back to the client, which is created as follows: hash(client IP + hash2).
 * 
 * So the chain goes as follows:
 * input hash = hash 1 = hash(*external IP address of client caller*.*his hashed password as stored in the server database*)
 * hash 2 = hash(client IP + hash1). This is stored in the database in the credential table.
 * hash 3 = hash(client IP + hash2). This is returned to the user and must be used to call a webservice.
 * Everytime you hash something, you *have* to use the same algorithm as was used to hash your password during registration, because if you don't, obviously the resulting hashes won't be the same.
 * If you don't know which one was used, ask the system admin.
 *
 * The can execute method will check the provided hash (hash 3) to see if it checks out,
 * after which it will consult with the rights and roles system to see if you are allowed to use the called webservice.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */

abstract class Webservice
{
    private $message;
    private $credential;
    private $ru;

    public function Webservice()
    {
        $this->ru = new RightsUtilities();
    }

    public static function factory($webservice_handler, $protocol = 'Soap', $implementation = 'Nusoap')
    {
        $file_protocol = Utilities :: camelcase_to_underscores($protocol);
        $file_implementation = Utilities :: camelcase_to_underscores($implementation);
        
        require_once dirname(__FILE__) . '/' . $file_protocol . '/' . $file_implementation . '/' . $file_protocol . '_' . $file_implementation . '_webservice.class.php';
        $class = $protocol . $implementation . 'Webservice';
        return new $class($webservice_handler);
    }

    abstract function provide_webservice($functions);

    /**
     * Call a webservice
     * @param $wsdl - the location of the webservice
     * @param $functions - array of functionnames, parameters and handler function
     * ex :: array(0 => (array('name' => functionname, 'parameters' => array of parameters, 'handler' => handler function)))
     */
    
    abstract function call_webservice($wsdl, $functions);

    abstract function raise_message($message);

    //abstract function raise_error($faultstring = 'unknown error', $faultcode = 'Client', $faultactor = NULL, $detail = NULL, $mode = null, $options = null);
    

    function validate_function($hash3) //hash 3
    {
        $wdm = WebserviceDataManager :: get_instance();
        $wdm->delete_expired_webservice_credentials();
        $credentials = $wdm->retrieve_webservice_credentials_by_ip($_SERVER['REMOTE_ADDR']);
        $credentials = $credentials->as_array();
        if (is_array($credentials))
        {
            foreach ($credentials as $c) //werkt
            {
                $h = Hashing :: hash($_SERVER['REMOTE_ADDR'] . $c->get_hash()); //hash 3 based on hash 2               
                

                if (strcmp($h, $hash3) === 0) //zijn gelijk
                {
                    return $c->get_user_id();
                }
            
            }
        }
        else
        {
            $this->message = Translation :: get('IncorrectIPAddress') . ': ' . $_SERVER['REMOTE_ADDR'] . '.';
        }
    }

    function get_end_time()
    {
        return (time() + (10 * 60)); //timeframe 10 mins
    

    }

    function get_create_time($time)
    {
        return date("l, F d, Y h:i", $time);
    }

    function check_time_left($endTime)
    {
        if (time() > $endtime)
        {
            $this->message = Translation :: get('YourAvailableTimeHasBeenUsedUp') . '.';
            $this->raise_message($this->message);
            return true;
        }
        else
        {
            $this->message = Translation :: get('YouHave') . ' ' . ($endTime - time()) . ' ' . Translation :: get('TimeLeft') . '.';
            $this->raise_message($this->message);
            return false;
        }
    }

    function validate_login($username, $input_hash) //hash 1 = ip+password
    {
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user_by_username($username);
        if (isset($user))
        {
            $hash = Hashing :: hash($_SERVER['REMOTE_ADDR'] . $user->get_password()); //hash 1
            if (strcmp($hash, $input_hash) == 0) //loginservice validate succesful, credential needed to validate the other webservices
            {
                $this->credential = new WebserviceCredential(array('user_id' => $user->get_id(), 'hash' => Hashing :: hash($_SERVER['REMOTE_ADDR'] . $hash), 'time_created' => time(), 'end_time' => $this->get_end_time(), 'ip' => $_SERVER['REMOTE_ADDR']));
                $this->credential->create(); //create credential with hash 2
                return Hashing :: hash($_SERVER['REMOTE_ADDR'] . $this->credential->get_default_property('hash')); //hash 3 based on hash 2, which resides in the credential object (as seen 2 lines above)
            }
            else
            {
                $this->message = Translation :: get('WrongHashValueSubmitted') . '.';
                return false;
            }
        }
        else
        {
            $this->message = Translation :: get('LoginError') . ': ' . Translation :: get('User') . $username . Translation :: get('DoesNotExist') . '.';
            return false;
        }
    }

    public function check_rights($webservicename, $userid)
    {
        $wm = WebserviceDataManager :: get_instance();
        $webservice = $wm->retrieve_webservice_by_name($webservicename);
        if (isset($webservice))
        {
            if ($this->ru->is_allowed('1', $webservice->get_id(), 'webservice', 'webservice', $userid))
            {
                return true;
            }
            else
            {
                $this->message = Translation :: get('YouAreNotAllowedToUseThisWebservice');
                return false;
            }
        }
        else
        {
            $this->message = Translation :: get('NoWebserviceByThatName');
            return false;
        }
    
    }

    public function can_execute($input_user, $webservicename)
    {
        $userid = $this->validate_function($input_user[hash]);
        if (! empty($userid) && $this->check_rights($webservicename, $userid))
            return true;
        else
            return false;
    }

    public function get_message()
    {
        return $this->raise_message($this->message);
    }

}

?>