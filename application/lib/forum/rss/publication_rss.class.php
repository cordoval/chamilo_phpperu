<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';
require_once dirname(__FILE__).'/../forum_manager/forum_manager.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/forum/forum_display.class.php';

class ForumPublicationRSS extends PublicationRSS
{
	function ForumPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo forum', htmlspecialchars(Path :: get(WEB_PATH)), 'Forum publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = ForumDataManager :: get_instance()->retrieve_forum_publications(null, null, 20);//, array('id', SORT_DESC));
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
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = ForumManager :: ACTION_VIEW;
		$params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
		$params[ForumManager :: PARAM_PUBLICATION_ID] = $pub->get_forum_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(ForumManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		if ($user->is_platform_admin() || $user->get_id() == $pub->get_author())
        {
            return true;
        }
        
        if ($pub->is_hidden())
        {
            return false;
        }
        
        return true;
	}
	
	function add_item($publication, $channel)
	{
		$co = $publication->get_forum_id();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		$channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
	}
}

?>