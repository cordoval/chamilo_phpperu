<?php
/**
 * $Id: webservice_credential.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */
class WebserviceCredential extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_HASH = 'hash';
    const PROPERTY_IP = 'ip';
    const PROPERTY_TIME_CREATED = 'time_created';
    const PROPERTY_END_TIME = 'end_time';

    /**
     * Get the default properties of all webservice_credentials.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_HASH, self :: PROPERTY_IP, self :: PROPERTY_TIME_CREATED, self :: PROPERTY_END_TIME);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebserviceDataManager :: get_instance();
    }

    /**
     * Returns the id of this webservice_credential.
     * @return int The id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the name of this webservice_credential.
     * @return String The name
     */
    function get_hash()
    {
        return $this->get_default_property(self :: PROPERTY_HASH);
    }

    /**
     * Returns the logged IP-address of this webservice_credential.
     * @return String IP
     */
    function get_ip()
    {
        return $this->get_default_property(self :: PROPERTY_IP);
    }

    /**
     * Returns the time this webservice_credential was created.
     * @return int
     */
    function get_time_created()
    {
        return $this->get_default_property(self :: PROPERTY_TIME_CREATED);
    }

    function get_end_time()
    {
        return $this->get_default_property(self :: PROPERTY_END_TIME);
    }

    /**
     * Sets the user_id of this credential.
     * @param int $webservice_id The webservice_id.
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Sets the hash of this webservice_credential.
     * @param String $hash the hash.
     */
    function set_hash($hash)
    {
        $this->set_default_property(self :: PROPERTY_HASH, $hash);
    }

    /**
     * Sets the logged ip of this webservice_credential.
     * @param String $ip the ip.
     */
    function set_ip($ip)
    {
        $this->set_default_property(self :: PROPERTY_IP, $ip);
    }

    /**
     * Sets the time this webservice_credential was created.
     * @param String $time_created the time_created.
     */
    function set_time_created($time_created)
    {
        $this->set_default_property(self :: PROPERTY_TIME_CREATED, $time_created);
    }

    function set_end_time($end_time)
    {
        $this->set_default_property(self :: PROPERTY_END_TIME, $end_time);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function create()
    {
        $wdm = WebserviceDataManager :: get_instance();
        return $wdm->create_webservice_credential($this);
    }
}
?>