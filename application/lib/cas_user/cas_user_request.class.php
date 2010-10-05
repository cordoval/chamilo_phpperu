<?php
/**
 * @author Hans De Bisschop
 */
class CasUserRequest extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'request';

    /**
     * CasUserRequest properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_FIRST_NAME = 'first_name';
    const PROPERTY_LAST_NAME = 'last_name';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_AFFILIATION = 'affiliation';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_REQUESTER_ID = 'requester_id';
    const PROPERTY_REQUEST_DATE = 'requested';
    const PROPERTY_STATUS = 'status';

    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_FIRST_NAME, self :: PROPERTY_LAST_NAME, self :: PROPERTY_EMAIL, self :: PROPERTY_AFFILIATION, self :: PROPERTY_MOTIVATION, self :: PROPERTY_REQUESTER_ID, self :: PROPERTY_REQUEST_DATE, self :: PROPERTY_STATUS);
    }

    function get_data_manager()
    {
        return CasUserDataManager :: get_instance();
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

    public function get_motivation()
    {
        return $this->get_default_property(self :: PROPERTY_MOTIVATION);
    }

    public function get_requester_id()
    {
        return $this->get_default_property(self :: PROPERTY_REQUESTER_ID);
    }

    public function get_request_date()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_DATE);
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

    public function set_motivation($motivation)
    {
        $this->set_default_property(self :: PROPERTY_MOTIVATION, $motivation);
    }

    public function set_requester_id($requester_id)
    {
        $this->set_default_property(self :: PROPERTY_REQUESTER_ID, $requester_id);
    }

    public function set_request_date($request_date)
    {
        $this->set_default_property(self :: PROPERTY_REQUEST_DATE, $request_date);
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
            case self :: STATUS_ACCEPTED :
                return Theme :: get_image('status_accepted');
                break;
            case self :: STATUS_PENDING :
                return Theme :: get_image('status_pending');
                break;
            case self :: STATUS_REJECTED :
                return Theme :: get_image('status_rejected');
                break;
        }
    }

    function is_pending()
    {
        return $this->get_status() == self :: STATUS_PENDING;
    }

    function is_rejected()
    {
        return $this->get_status() == self :: STATUS_REJECTED;
    }

    function get_requester_user()
    {
        $user = UserDataManager :: get_instance()->retrieve_user($this->get_requester_id());
        return ($user instanceof User ? $user : '');
    }
}

?>