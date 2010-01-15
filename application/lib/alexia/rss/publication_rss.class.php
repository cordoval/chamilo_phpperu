<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../alexia_data_manager.class.php';

class AlexiaPublicationRSS extends PublicationRSS
{
	function AlexiaPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo Alexia', htmlspecialchars(Path :: get(WEB_PATH)), 'Alexia publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = AlexiaDataManager :: get_instance()->retrieve_alexia_publications(null, null, 20);
		$publications = array();
		while ($pub = $pubs->next_result())
		{
			$publications[] = $pub;
		}
		return $publications;
	}
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = AlexiaManager :: ACTION_VIEW_ALEXIA_PUBLICATION;
		$params[AlexiaManager :: PARAM_ALEXIA_PUBLICATION] = $pub->get_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(AlexiaManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->is_visible_for_target_user($user->get_id());
	}
}

?>