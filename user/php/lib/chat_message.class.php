<?php
/**
 * $Id: chat_message.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */


class ChatMessage extends DataClass
{
	const CLASS_NAME					= __CLASS__;
	
	const PROPERTY_FROM_USER_ID = 'from_user_id';
	const PROPERTY_TO_USER_ID = 'to_user_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_MESSAGE = 'message';

	/**
	 * Get the default properties of all users quota objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_FROM_USER_ID, self :: PROPERTY_TO_USER_ID, self :: PROPERTY_DATE, self :: PROPERTY_MESSAGE));
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return UserDataManager :: get_instance();	
	}

	function get_from_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_FROM_USER_ID);
	}
	
	function set_from_user_id($from_user_id)
	{
		$this->set_default_property(self :: PROPERTY_FROM_USER_ID, $from_user_id);
	}
	
	function get_to_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_TO_USER_ID);
	}
	
	function set_to_user_id($to_user_id)
	{
		$this->set_default_property(self :: PROPERTY_TO_USER_ID, $to_user_id);
	}

	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}
	
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}
	
	function get_message()
	{
		return $this->get_default_property(self :: PROPERTY_MESSAGE);
	}
	
	function set_message($message)
	{
		$this->set_default_property(self :: PROPERTY_MESSAGE, $message);
	}
	
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

}
?>