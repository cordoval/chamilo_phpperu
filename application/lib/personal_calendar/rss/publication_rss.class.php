<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager/personal_calendar_manager.class.php';

class PersonalCalendarPublicationRSS extends PublicationRSS
{
	function PersonalCalendarPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo Personal Calendar', htmlspecialchars(Path :: get(WEB_PATH)), 'Personal calendar publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = PersonalCalendarDataManager :: get_instance()->retrieve_calendar_event_publications(null, null, 20);//, array('id', SORT_DESC));
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
		$co = $publication->get_content_object_id();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		$channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
	}
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = PersonalCalendarManager :: ACTION_VIEW_PUBLICATION;
		$params[PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID] = $pub->get_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(PersonalCalendarManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return ($pub->is_target($user) || $user->get_id() == $pub->get_publisher());
	}
}

?>