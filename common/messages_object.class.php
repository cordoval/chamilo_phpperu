<?php
/**
 * General object which uses a messages system to communicate
 */

abstract class MessagesObject
{
	private $messages;
		
	function MessagesObject()
	{
		$this->messages = array();	
	}
	
	function add_message($message)
	{
		$this->messages[] = $message;
	}
	
	function clear_messages()
	{
		$this->messages = array();
	}
	
	function get_messages()
	{
		return $this->messages;
	}
	
	function set_messages($messages = array())
	{
		$this->messages = $messages;
	}
	
	function get_messages_as_string()
	{
		return implode("<br />\n", $this->messages);
	}
	
}

?>