<?php

namespace common\libraries;

/**
 * Class to provide authentication for webservices
 *
 * Authentication uses the Authentication header that can be sent to the webservice
 * Secures the authentication through signing a combination of several parameters through HMAC-SHA512
 * The shared secret key for signing can be found in the users table
 * The combination of username / timestamp / nonce should be unique so requests can not be hijacked and executed again
 * Parameter list is username&nonce&password&timestamp&ipaddress
 *
 * Example
 * GET /rest.php?application=user&object=user HTTP/1.1
 * Host: localhost
 * Authorization: Chamilo realm="Chamilo"
 *   username="admin",
 *   timestamp="137131200",
 *   nonce="wIjqoS",
 *   signature="74KNZJeDHnMBp0EMJ9ZHt%2FXKycU%3D"
 *
 */
class ChamiloWebserviceAuthentication extends WebserviceAuthentication
{
    const PARAM_AUTHORIZATION = 'authorization';
    const PARAM_USERNAME = 'username';
    const PARAM_TIMESTAMP = 'timestamp';
    const PARAM_NONCE = 'nonce';
    const PARAM_SIGNATURE = 'signature';
    const PARAM_REMOTE_ADDRESS = 'REMOTE_ADDR';
    const PARAM_HASH_ALGORITHM = 'sha512';

    public function is_valid()
    {
        $headers = apache_request_headers();
        if(!$headers[self :: PARAM_AUTHORIZATION])
        {
            return false;
        }

        $authorization = explode(',', $headers[self :: PARAM_AUTHORIZATION]);
        $username = $authorization[self :: PARAM_USERNAME];

        $user = UserDataManager :: get_instance()->retrieve_user_by_username($username);
        if(!$user)
        {
            return false;
        }

        $timestamp = $authorization[self :: PARAM_TIMESTAMP];
        if($timestamp < time())
        {
            return false;
        }

        $signature = $this->calculate_signature($authorization, $user);

        return ($signature == $authorization[self :: PARAM_SIGNATURE]);
    }

    private function calculate_signature($authorization, $user)
    {
        $base_text = $authorization[self :: PARAM_USERNAME] . '&' . $authorization[self :: PARAM_NONCE] . '&' . $user->get_password() . '
            &' . $authorization[self :: PARAM_TIMESTAMP] . '&' . $_SERVER[self :: PARAM_REMOTE_ADDRESS];

        $secret_key = $user->get_security_token();

        return hash_hmac(self :: PARAM_HASH_ALGORITHM, $base_text, $secret_key);
    }

}
