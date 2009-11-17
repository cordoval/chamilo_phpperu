<?php
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
    const PROPERTY_ISOCODE = 'isocode';
    const PROPERTY_FOLDER = 'folder';
    const PROPERTY_AVAILABLE = 'available';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ORIGINAL_NAME, self :: PROPERTY_ENGLISH_NAME, self :: PROPERTY_ISOCODE, self :: PROPERTY_FOLDER, self :: PROPERTY_AVAILABLE));
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
    function get_isocode()
    {
        return $this->get_default_property(self :: PROPERTY_ISOCODE);
    }

    /**
     * Returns the sender of this PMP object
     * @return int the sender
     */
    function get_folder()
    {
        return $this->get_default_property(self :: PROPERTY_FOLDER);
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
    function set_isocode($isocode)
    {
        $this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
    }

    /**
     * Sets the sender of this PMP.
     * @param int $sender the Sender.
     */
    function set_folder($folder)
    {
        $this->set_default_property(self :: PROPERTY_FOLDER, $folder);
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
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
