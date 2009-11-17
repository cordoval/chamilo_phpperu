<?php

/**
 * Description of validation
 *
 * @author Pieter Hens
 * @package admin.lib
 * $Id: validation.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */


class Validation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_PID = 'publication_id';
    const PROPERTY_CID = 'complex_id';
    const PROPERTY_VALIDATED = 'validated';
    const PROPERTY_OWNER = 'owner_id';

    /**
     * Get the default properties of all validations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_PID, self :: PROPERTY_CID, self :: PROPERTY_VALIDATED, self :: PROPERTY_OWNER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the application of this validation object
     * @return string The validation application
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Returns publication id
     * @return integer the pid
     */
    function get_pid()
    {
        return $this->get_default_property(self :: PROPERTY_PID);
    }

    /**
     * Returns complex id (id within complex learning object)
     * @return integer the cid
     */
    function get_cid()
    {
        return $this->get_default_property(self :: PROPERTY_CID);
    }

    /**
     * Returns validation id
     * @return integer the fid
     */
    function get_validated()
    {
        return $this->get_default_property(self :: PROPERTY_VALIDATED);
    }

    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER);
    }

    /**
     * Sets the application of this validation.
     * @param string $application the validation application.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the pid of this validation.
     * @param integer $pid the pid.
     */
    function set_pid($pid)
    {
        $this->set_default_property(self :: PROPERTY_PID, $pid);
    }

    /**
     * Sets the cid of this validation.
     * @param integer $cid the cid.
     */
    function set_cid($cid)
    {
        $this->set_default_property(self :: PROPERTY_CID, $cid);
    }

    /**
     * Sets the validated of this validation.
     * @param integer $validated the validated.
     */
    function set_validated($validated)
    {
        $this->set_default_property(self :: PROPERTY_VALIDATED, $validated);
    }

    function set_owner($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_validation_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_owner());
    }
}
?>
