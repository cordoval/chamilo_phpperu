<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feedback
 * $Id: feedback_publication.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib
 * @author Pieter Hens
 */


class FeedbackPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_PID = 'publication_id';
    const PROPERTY_CID = 'complex_id';
    const PROPERTY_FID = 'feedback_id';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_MODIFICATION_DATE = 'modification_date';

    /**
     * Get the default properties of all feedbacks.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_PID, self :: PROPERTY_CID, self :: PROPERTY_FID, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_MODIFICATION_DATE));
    }

    /*
	 * Gets the table name for this class
	 */
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(__CLASS__);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the application of this feedback object
     * @return string The feedback application
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
     * Returns feedback id
     * @return integer the fid
     */
    function get_fid()
    {
        return $this->get_default_property(self :: PROPERTY_FID);
    }

    /**
     * Sets the application of this feedback.
     * @param string $application the feedback application.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the pid of this feedback.
     * @param integer $pid the pid.
     */
    function set_pid($pid)
    {
        $this->set_default_property(self :: PROPERTY_PID, $pid);
    }

    /**
     * Sets the cid of this feedback.
     * @param integer $cid the cid.
     */
    function set_cid($cid)
    {
        $this->set_default_property(self :: PROPERTY_CID, $cid);
    }

    /**
     * Sets the fid of this feedback.
     * @param integer $fid the fid.
     */
    function set_fid($fid)
    {
        $this->set_default_property(self :: PROPERTY_FID, $fid);
    }
    
	/**
     * Returns creation_date
     * @return integer the creation_date
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }
    
 	/**
     * Sets the creation_date of this feedback.
     * @param integer $creation_date the creation_date.
     */
    function set_creation_date($creation_date)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
    }
    
	/**
     * Returns modification_date
     * @return integer the modification_date
     */
    function get_modification_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFICATION_DATE);
    }
    
 	/**
     * Sets the modification_date of this feedback.
     * @param integer $modification_date the modification_date.
     */
    function set_modification_date($modification_date)
    {
        $this->set_default_property(self :: PROPERTY_MODIFICATION_DATE, $modification_date);
    }
}
?>
