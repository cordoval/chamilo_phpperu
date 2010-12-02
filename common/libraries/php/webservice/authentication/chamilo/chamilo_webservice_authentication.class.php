<?php

namespace common\libraries;

/**
 * Class to provide authentication for webservices
 *
 * Authentication uses the Authentication header that can be sent to the webservice
 * Secures the authentication through signing a combination of several parameters through either HMAC-SHA1 or RSA-SHA1
 * The shared secret key for signing can be found in the users table
 * The combination of username / timestamp / nonce should be unique so requests can not be hijacked and executed again
 * Parameter list is username&nonce&password&timestamp&ipaddress
 *
 * Example
 * GET /rest.php?application=user&object=user HTTP/1.1
 * Host: localhost
 * Authorization: Chamilo realm="Chamilo"
 *   username="admin",
 *   signature_method="HMAC-SHA1",
 *   timestamp="137131200",
 *   nonce="wIjqoS",
 *   signature="74KNZJeDHnMBp0EMJ9ZHt%2FXKycU%3D"
 *
 */
class ChamiloWebserviceAuthentication extends WebserviceAuthentication
{
    const PARAM_AUTHORIZATION = 'authorization';
    const PARAM_USERNAME = 'username';
    const PARAM_SIGNATURE_METHOD = 'signature_method';
    const PARAM_TIMESTAMP = 'timestamp';
    const PARAM_NONCE = 'nonce';
    const PARAM_SIGNATURE = 'signature';

    public function is_valid()
    {
        $headers = apache_request_headers();
        $authorization = explode(',', $headers[self :: PARAM_AUTHORIZATION]);
        
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    private function calculate_signature($authorization)
    {
        
    }
}
