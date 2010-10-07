<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_libraries_path().'/rss/publication_rss.class.php';
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_data_manager.class.php';

class WikiPublicationRSS extends PublicationRSS
{
	function WikiPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo wiki', htmlspecialchars(Path :: get(WEB_PATH)), 'Wiki publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = WikiDataManager :: get_instance()->retrieve_wiki_publications(null, 0, 20);//, array('id', SORT_DESC));
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
		$params[Application :: PARAM_ACTION] = WikiManager :: ACTION_VIEW_COURSE;
		$params[WikiManager :: PARAM_COURSE_USER] = $pub->get_course_id();
		$params[ContentObjectPublication :: PROPERTY_TOOL] = $pub->get_tool();
		return Path :: get(WEB_PATH).Redirect :: get_link(WikiManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		if ($user->is_platform_admin() || $user->get_id() == $pub->get_publisher())
        {
            return true;
        }
        
        if ($pub->is_hidden())
        {
            return false;
        }
        
        $time = time();
        
        if (!$pub->is_forever())
        {
	        if ($time < $pub->get_from_date() || $time > $pub->get_to_date())
	        {
	            return false;
	        }
        }
        
        return true;
	}
}

?>