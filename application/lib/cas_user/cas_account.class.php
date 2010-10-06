<?php
/**
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/cas_account_data_manager/cas_account_data_manager.class.php';

class CasAccount extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'external_users';

    /**
     * CasAccount properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_FIRST_NAME = 'firstname';
    const PROPERTY_LAST_NAME = 'lastname';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_AFFILIATION = 'affiliation';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_GROUP = 'group';
    const PROPERTY_STATUS = 'status';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_FIRST_NAME, self :: PROPERTY_LAST_NAME, self :: PROPERTY_EMAIL, self :: PROPERTY_AFFILIATION, self :: PROPERTY_PASSWORD, self :: PROPERTY_GROUP, self :: PROPERTY_STATUS));
    }

    function get_data_manager()
    {
        return CasAccountDataManager :: get_instance();
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    public function get_first_name()
    {
        return $this->get_default_property(self :: PROPERTY_FIRST_NAME);
    }

    public function get_last_name()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_NAME);
    }

    public function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    public function get_affiliation()
    {
        return $this->get_default_property(self :: PROPERTY_AFFILIATION);
    }

    public function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }

    public function get_group()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP);
    }

    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    public function set_first_name($first_name)
    {
        $this->set_default_property(self :: PROPERTY_FIRST_NAME, $first_name);
    }

    public function set_last_name($last_name)
    {
        $this->set_default_property(self :: PROPERTY_LAST_NAME, $last_name);
    }

    public function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    public function set_affiliation($affiliation)
    {
        $this->set_default_property(self :: PROPERTY_AFFILIATION, $affiliation);
    }

    public function set_password($password)
    {
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    public function set_group($group)
    {
        $this->set_default_property(self :: PROPERTY_GROUP, $group);
    }

    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_status_icon()
    {
        switch ($this->get_status())
        {
            case self :: STATUS_ENABLED :
                return Theme :: get_image(self :: PROPERTY_STATUS . '_enabled');
                break;
            case self :: STATUS_DISABLED :
                return Theme :: get_image(self :: PROPERTY_STATUS . '_disabled');
                break;
        }
    }

    function is_enabled()
    {
        return $this->get_status() == self :: STATUS_ENABLED;
    }
}

?>