<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\DataClass;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
/**
 * $Id: language.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */

class Language extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ORIGINAL_NAME = 'original_name';
    const PROPERTY_ENGLISH_NAME = 'english_name';
    const PROPERTY_FAMILY = 'family';
    const PROPERTY_ISOCODE = 'isocode';
    const PROPERTY_AVAILABLE = 'available';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_ORIGINAL_NAME,
                self :: PROPERTY_ENGLISH_NAME,
                self :: PROPERTY_FAMILY,
                self :: PROPERTY_ISOCODE,
                self :: PROPERTY_AVAILABLE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the learning object id from this PMP object
     * @return int The personal message ID
     */
    function get_original_name()
    {
        return $this->get_default_property(self :: PROPERTY_ORIGINAL_NAME);
    }

    /**
     * Returns the status of this PMP object
     * @return int the status
     */
    function get_english_name()
    {
        return $this->get_default_property(self :: PROPERTY_ENGLISH_NAME);
    }

    /**
     * Returns the user of this PMP object
     * @return int the user
     */
    function get_family()
    {
        return $this->get_default_property(self :: PROPERTY_FAMILY);
    }

    /**
     * Returns the user of this PMP object
     * @return int the user
     */
    function get_isocode()
    {
        return $this->get_default_property(self :: PROPERTY_ISOCODE);
    }

    /**
     * Returns the recipient of this PMP object
     * @return int the recipient
     */
    function get_available()
    {
        return $this->get_default_property(self :: PROPERTY_AVAILABLE);
    }

    /**
     * Sets the learning object id of this PMP.
     * @param Int $id the personal message ID.
     */
    function set_original_name($original_name)
    {
        $this->set_default_property(self :: PROPERTY_ORIGINAL_NAME, $original_name);
    }

    /**
     * Sets the status of this PMP.
     * @param int $status the Status.
     */
    function set_english_name($english_name)
    {
        $this->set_default_property(self :: PROPERTY_ENGLISH_NAME, $english_name);
    }

    /**
     * Sets the user of this PMP.
     * @param int $user the User.
     */
    function set_family($family)
    {
        $this->set_default_property(self :: PROPERTY_FAMILY, $family);
    }

    /**
     * Sets the user of this PMP.
     * @param int $user the User.
     */
    function set_isocode($isocode)
    {
        $this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
    }

    /**
     * Sets the recipient of this PMP.
     * @param int $recipient the user_id of the recipient.
     */
    function set_available($available)
    {
        $this->set_default_property(self :: PROPERTY_AVAILABLE, $available);
    }

    function is_available()
    {
        return $this->get_available();
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function create()
    {
        if (! parent :: create())
        {
            return false;
        }

        $registration = new Registration();
        $registration->set_name($this->get_isocode());
        $registration->set_type(Registration :: TYPE_LANGUAGE);
        $registration->set_category($this->get_family());
        $registration->set_version('1.0.0');
        $registration->set_status(Registration :: STATUS_ACTIVE);
        return $registration->create();
    }

    function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_LANGUAGE);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $this->get_english_name());
        $condition = new AndCondition($conditions);

        $registration = AdminDataManager :: get_instance()->retrieve_registrations($condition)->next_result();
        return $registration->delete();
    }
}
?>