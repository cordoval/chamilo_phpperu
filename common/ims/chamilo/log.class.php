<?php

/**
 *
 * Message log.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
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

	public function write($messages, $type = self::TYPE_INFO){
		if(empty($messages)){
			return;
		}

		if(is_array($messages)){
			foreach($messages as $m){
				$this->messages[$type][] = $m;
			}
		}else{
			$this->messages[$type][] = $messages;
		}
	}

	public function message($messages){
		$this->write($messages, self::TYPE_INFO);
	}

	public function warning($messages){
		$this->write($messages, self::TYPE_WARNING);
	}

	public function error($messages){
		$this->write($messages, self::TYPE_ERROR);
	}

	public function translate($messages, $type = self::TYPE_INFO){
		if(is_array($messages)){
			foreach($messages as $m){
				$this->translate($m, $type);
			}
		}else{
			$this->write(Translation::translate($messages), $type);
		}
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
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class OnlineLog extends log{

	public function write($messages, $type = self::TYPE_INFO){
		if(is_array($messages)){
			foreach($messages as $m){
				$this->write($m, $type);
			}
		}else{
			switch($type){
				case self::TYPE_INFO:
	        		Application::display_message($messages);
					break;
				case self::TYPE_WARNING:
	        		Application::display_warning_message($messages);
					break;
				case self::TYPE_ERROR:
	        		Application::display_error_message($messages);
					break;
				default:
					throw new Exception('Not implemented');
			}
		}
	}
}

/**
 * Empty log. Does nothing.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
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

	public function write($messages){
		return true;
	}
	public function message($messages){
		return true;
	}

	public function warning($messages){
		return true;
	}

	public function error($messages){
		return true;
	}

	public function translate($messages){
		return true;
	}

	public function clear(){
		return true;
	}

}