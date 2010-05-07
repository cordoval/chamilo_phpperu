<?php
/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CommonRequest extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const ALLOWED_DECISION = 2;
    const DENIED_DECISION = 1;
    const NO_DECISION = 0;
    
    const SUBSCRIPTION_REQUEST = 'subscription_request';
	const CREATION_REQUEST = 'creation_request';
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_DECISION_DATE = 'decision_date';
    const PROPERTY_DECISION = 'decision';

    static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
        	   array_merge($extended_property_names, array(
        		  self :: PROPERTY_USER_ID,
        		  self :: PROPERTY_SUBJECT,
        		  self :: PROPERTY_MOTIVATION,
        		  self :: PROPERTY_CREATION_DATE,
        		  self :: PROPERTY_DECISION_DATE,
        		  self :: PROPERTY_DECISION)));
    }

    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }
    
    function get_user_id()
    {
    	return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
    function get_subject()
    {
    	return $this->get_default_property(self :: PROPERTY_SUBJECT);
    }
    
    function get_motivation()
    {
        return $this->get_default_property(self :: PROPERTY_MOTIVATION);
    }

    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }
    
    function get_decision_date()
    {
        return $this->get_default_property(self :: PROPERTY_DECISION_DATE);
    }
    
    function get_decision()
    {
        return $this->get_default_property(self :: PROPERTY_DECISION);
    }
    
    function set_user_id($user_id)
    {
    	return $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
    function set_subject($subject)
    {
    	$this->set_default_property(self :: PROPERTY_SUBJECT, $subject);
    }

    function set_motivation($motivation)
    {
        $this->set_default_property(self :: PROPERTY_MOTIVATION, $motivation);
    }
    
    function set_creation_date($creation_date)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
    }   

    function set_decision_date($decision_date)
    {
         $this->set_default_property(self :: PROPERTY_DECISION_DATE, $decision_date);
    }
    
    function set_decision($decision)
    {
         $this->set_default_property(self :: PROPERTY_DECISION, $decision);
    }
    
	static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>