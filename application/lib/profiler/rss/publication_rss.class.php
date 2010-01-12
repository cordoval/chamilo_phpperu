<?php
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../profiler_data_manager.class.php';
require_once dirname(__FILE__).'/../profiler_manager/profiler_manager.class.php';

class ProfilerPublicationRSS extends PublicationRSS
{
	function ProfilerPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo Profiler', htmlspecialchars(Path :: get(WEB_PATH)), 'Profiler publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = ProfilerDataManager :: get_instance()->retrieve_profile_publications(null, null, 20);//, array('id', SORT_DESC));
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
		$co = $publication->get_profile();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		$channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
	}
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = ProfilerManager :: ACTION_VIEW_PUBLICATION;
		//$params[PortfolioManager :: PARAM_USER_ID] = $pub->get_publisher();
		$params[ProfilerManager :: PARAM_PROFILE_ID] = $pub->get_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(ProfilerManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return true;
		//return $pub->is_visible_for_target_user($user->get_id());
	}
}

?>