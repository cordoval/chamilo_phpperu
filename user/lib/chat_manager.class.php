<?php
/**
 * $Id: chat_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */


class ChatManager
{
	/**
	 * Maximum lines to be retrieved from chat
	 */
	const MAX_LINES = 20;
	
	/**
	 * The current client
	 *
	 * @var User
	 */
	private $local_user;
	
	/**
	 * The remote user with who'm de local user will chat
	 *
	 * @var User
	 */
	private $remote_user;
	
	/**
	 * The component on which this chat manager runs
	 *
	 * @var unknown_type
	 */
	private $parent;
	
	/**
	 * The last message date, when retrieving new messages with ajax, the last message date is used)
	 *
	 * @var Integer
	 */
	private $last_message_date;
	
	function ChatManager($local_user, $remote_user, $parent)
	{
		$this->local_user = $local_user;
		$this->remote_user = $remote_user;
		$this->parent = $parent;
	}
	
	function to_html()
	{
		$form = new FormValidator('chat_' . $this->local_user->get_id() . '_' . $this->remote_user->get_id(), 'post', $this->parent->get_url());
		
		$form->addElement('textarea', 'chat_window', '', array('style' => 'width: 99%; height: 300px;', 'id' => 'chat_window'));
		$form->addElement('html', '<br /><br />');
		$form->addElement('text', 'chat_message', '', array('style' => 'width: 80%; float: left; height: 25px;', 'id' => 'chat_message'));
		$form->addElement('style_submit_button', 'send', Translation :: get('Send'), array('class' => 'positive', 'style' => 'float: right;', 'id' => 'send_message'));
		$renderer = $form->defaultRenderer();
		$renderer->setElementTemplate('{element}');
		
		if($form->validate())
		{
			$this->send_message($form->exportValue('chat_message'));
		}
		
		$this->set_defaults($form);
		
		$js = array();
		$js[] = '<script language="JavaScript">';
		$js[] = '  var last_message_date="' . $this->last_message_date . '";';
		$js[] = '  var from_user_id="' . $this->local_user->get_id() . '";';
		$js[] = '  var to_user_id="' . $this->remote_user->get_id() . '";';
		$js[] = '</script>';
		$js[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/chat_manager.js');
		
		return implode("\n", $js) . $form->toHtml();
	}
	
	function to_xml($last_message_date)
	{
		$xml = array();
		
		$xml[] = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<messages>';
		
		$messages = $this->retrieve_chat_messages($last_message_date)->as_array();
		$messages = array_reverse($messages);
		
		foreach($messages as $message)
		{
			$user = $this->local_user == $message->get_from_user_id() ? $this->local_user : $this->remote_user;
			$xml[] = '<message message="' . '[' . date('H:i:s', $message->get_date()) . '] ' . $user->get_fullname() . ' ' . Translation :: get('Says') . ': ' . $message->get_message() . '" date="' . $message->get_date() . '" />';
		}
		
		$xml[] = '</messages>';
		
		return implode("\n", $xml);
	}
	
	function send_message($message)
	{
		$chat_message = new ChatMessage();
		$chat_message->set_from_user_id($this->local_user->get_id());
		$chat_message->set_to_user_id($this->remote_user->get_id());
		$chat_message->set_message($message);
		$chat_message->set_date(time());
		$chat_message->create();		
	}
	
	function set_defaults($form)
	{
		$messages = $this->retrieve_chat_messages()->as_array();
		$html = array();
		
		$messages = array_reverse($messages);
		
		foreach($messages as $message)
		{
			$user = $this->local_user == $message->get_from_user_id() ? $this->local_user : $this->remote_user;
			$html[] = '[' . date('H:i:s', $message->get_date()) . '] ' . $user->get_fullname() . ' ' . Translation :: get('Says') . ': ' . $message->get_message();
			$this->last_message_date = $message->get_date();
		}
		
		$form->setConstants(array('chat_window' => implode("\n", $html) . "\n"));
	}
	
	function retrieve_chat_messages($last_message_date = null)
	{
		$order[] = new ObjectTableOrder(ChatMessage :: PROPERTY_DATE, SORT_DESC);
		
		$conditions = array();
		$conditions1 = array();
		$conditions2 = array();
		
		$conditions1[] = new EqualityCondition(ChatMessage :: PROPERTY_FROM_USER_ID, $this->local_user->get_id());
		$conditions1[] = new EqualityCondition(ChatMessage :: PROPERTY_TO_USER_ID, $this->remote_user->get_id());
		$conditions[] = new AndCondition($conditions1);
		
		$conditions2[] = new EqualityCondition(ChatMessage :: PROPERTY_FROM_USER_ID, $this->remote_user->get_id());
		$conditions2[] = new EqualityCondition(ChatMessage :: PROPERTY_TO_USER_ID, $this->local_user->get_id());
		$conditions[] = new AndCondition($conditions2);
		
		$condition = new OrCondition($conditions);
		
		if($last_message_date)
		{
			$conditions = array();
			$conditions[] = $condition;
			$conditions[] = new InequalityCondition(ChatMessage :: PROPERTY_DATE, InequalityCondition :: GREATER_THAN, $last_message_date);
			$condition = new AndCondition($conditions);
		}
		
		$chat_messages = UserDataManager :: get_instance()->retrieve_chat_messages($condition, 0, self :: MAX_LINES, $order);
		
		return $chat_messages;
	}
}

?>