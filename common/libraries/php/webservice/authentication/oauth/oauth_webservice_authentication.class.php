<?php

namespace common\libraries;

/**
 * Class to provide authentication for webservices
 */
class OauthWebserviceAuthentication extends WebserviceAuthentication
{
    public function is_valid() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }
}

?>
