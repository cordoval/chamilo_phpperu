<?php

/**
 * Message log.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class Log{
	
	const TYPE_MESSAGE = 'message';
	const TYPE_WARNING = 'warning';
	const TYPE_ERROR = 'error';
	
	protected $messages = array();
	
	public function __construct(){
		$this->clear();
	}
	
	public function get_messages(){
		return $this->messages[self::TYPE_MESSAGE];
	}
	
	public function get_warnings(){
		return $this->messages[self::TYPE_WARNING];
	}
	
	public function get_errors(){
		return $this->messages[self::TYPE_ERROR];
	}
	
	public function write($message, $type = self::TYPE_INFO){
		$this->messages[$type][] = $message;
	}
	
	public function message($message){
		$this->write($message, self::TYPE_INFO);
	}
	
	public function warning($message){
		$this->write($message, self::TYPE_WARNING);
	}
	
	public function error($message){
		$this->write($message, self::TYPE_ERROR);
	}
	
	public function translate($message, $type = self::TYPE_INFO){
		$this->write(Translation::translate($message), $type);
	}
	
	public function clear(){
		$messages = array();
		$messages[self::TYPE_MESSAGE] = array();
		$messages[self::TYPE_WARNING] = array();
		$messages[self::TYPE_ERROR] = array();
		$this->messages = $messages;
	}
}

/**
 * Online log. Output messages on main output as soon as received.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class OnlineLog extends log{

	public function write($message, $type = self::TYPE_INFO){
		switch($type){
			case self::TYPE_INFO:
        		Application::display_message($message);
				break;
			case self::TYPE_WARNING:
        		Application::display_warning_message($message);
				break;
			case self::TYPE_ERROR:
        		Application::display_error_message($message);
				break;
			default:
				throw new Exception('Not implemented');
		}
	}
}

/**
 * Empty log. Does nothing.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class EmptyLog extends Log{

	public function get_messages(){
		return array();
	}
	
	public function get_warnings(){
		return array();
	}
	
	public function get_errors(){
		return array();
	}
	
	public function write($message){
		return true;
	}
	public function message($message){
		return true;
	}
	
	public function warning($message){
		return true;
	}
	
	public function error($message){
		return true;
	}
	
	public function translate($message){
		return true;
	}
	
	public function clear(){
		return true;
	}
	
}