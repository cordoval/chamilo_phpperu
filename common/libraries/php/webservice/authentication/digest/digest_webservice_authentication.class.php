<?php

namespace common\libraries;

use user\UserDataManager;

/**
 * Class to provide authentication for webservices through digest method (adapted from php example)
 * WARNING: This can currently only be used by automatic scripts that adapt the calculation of the response value to work with an md5/sha1 hash of the password (depending on the platforms hashing algorithm)
 */
class DigestWebserviceAuthentication extends WebserviceAuthentication
{
    const PARAM_AUTH_DIGEST = 'PHP_AUTH_DIGEST';
    const PARAM_REQUEST_METHOD = 'REQUEST_METHOD';

    const PARAM_REALM = 'chamilo2';
    const PARAM_NONCE = 'nonce';
    const PARAM_NC = 'nc';
    const PARAM_CNONCE = 'cnonce';
    const PARAM_QOP = 'qop';
    const PARAM_USERNAME = 'username';
    const PARAM_URI = 'uri';
    const PARAM_RESPONSE = 'response';

    function is_valid()
    {
        if (empty($_SERVER[self :: PARAM_AUTH_DIGEST]))
        {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="' . self :: PARAM_REALM . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5(self :: PARAM_REALM) . '"');

            return false;
        }
        else
        {
            $auth_digest = $_SERVER[self :: PARAM_AUTH_DIGEST];
            $data = $this->http_digest_parse($auth_digest);
            if(!$data)
            {
                return false;
            }
            
            $username = $data[self :: PARAM_USERNAME];
            $user = UserDataManager :: get_instance()->retrieve_user_by_username($username);
            if(!$user)
            {
                return false;
            }

            $valid_response = $this->calculate_valid_response($data, $user->get_password());
            return ($data[self :: PARAM_RESPONSE] == $valid_response);
        }
    }

    /**
     * Calculates the valid response
     * TODO: problem because password needs to be plain and plain password is not stored in chamilo
     */
    private function calculate_valid_response($data, $password)
    {
        $A1 = md5($data[self :: PARAM_USERNAME] . ':' . self :: PARAM_REALM . ':' . $password);
        $A2 = md5($_SERVER[self :: PARAM_REQUEST_METHOD].':'.$data[self :: PARAM_URI]);
        return md5($A1.':'.$data[self :: PARAM_NONCE].':'.$data[self :: PARAM_NC].':'.$data[self :: PARAM_CNONCE].':'.$data[self :: PARAM_QOP].':'.$A2);
    }

    private function http_digest_parse($auth_digest)
    {
        // protect against missing data
        $needed_parts = array(self :: PARAM_NONCE => 1, self :: PARAM_NC => 1, self :: PARAM_CNONCE => 1, self :: PARAM_QOP => 1, self :: PARAM_USERNAME => 1, self :: PARAM_URI => 1, self :: PARAM_RESPONSE => 1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $auth_digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m)
        {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

}

?>
