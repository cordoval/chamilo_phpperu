<?php

namespace application\personal_messenger;

use common\libraries\BasicApplication;
use common\libraries\WebApplication;
use common\libraries\PublicationRSS;
use common\libraries\Path;
use repository\RepositoryDataManager;
use common\libraries\Application;
use common\libraries\Redirect;

require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once BasicApplication :: get_common_libraries() . 'rss/publication_rss.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'data_manager/database.class.php';

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
		$params[PersonalMessengerManager :: PARAM_FOLDER] = PersonalMessengerManager :: FOLDER_INBOX;
		return Path :: get(WEB_PATH).Redirect :: get_link(PersonalMessengerManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->get_user() == $user->get_id();
	}
}

?>