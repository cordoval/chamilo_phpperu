<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';
require_once dirname(__FILE__).'/../personal_messenger_manager/personal_messenger_manager.class.php';

class PersonalMessengerPublicationRSS extends PublicationRSS
{
	function PersonalMessengerPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo Personal Messenger', htmlspecialchars(Path :: get(WEB_PATH)), 'Personal messenger publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publications(null, null, 20);//, array('id', SORT_DESC));
		$publications = array();
		while ($pub = $pubs->next_result())
		{
			if ($this->is_visible_for_user($user, $pub))
			{
				$publications[] = $pub;
			}
		}
		return $publications;
	}
	
	function add_item($publication, $channel)
	{
		$co = $publication->get_personal_message();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		$channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
	}
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = PersonalMessengerManager :: ACTION_VIEW_PUBLICATION;
		$params[PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID] = $pub->get_id();
		$params[PersonalMessengerManager :: PARAM_FOLDER] = PersonalMessengerManager :: ACTION_FOLDER_INBOX;
		return Path :: get(WEB_PATH).Redirect :: get_link(PersonalMessengerManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->get_user() == $user->get_id();
	}
}

?>