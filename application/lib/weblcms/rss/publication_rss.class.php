<?php
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';

class WeblcmsPublicationRSS extends PublicationRSS
{
	function WeblcmsPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo weblcms', 'http://localhost', 'Weblcms publications', 'http://localhost');
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new(null, array(), 0, 20);//, array('id', SORT_DESC));
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
		$params[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
		$params[WeblcmsManager :: PARAM_COURSE_USER] = $pub->get_course_id();
		$params[ContentObjectPublication :: PROPERTY_TOOL] = $pub->get_tool();
		return Path :: get(WEB_PATH).Redirect :: get_link(WeblcmsManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		if ($user->is_platform_admin() || $user->get_id() == $pub->get_publisher_id())
        {
            return true;
        }
        
        if ($pub->get_target_course_groups() || $pub->get_target_users())
        {
            $allowed = false;
            
            if (in_array($user->get_id(), $pub->get_target_users()))
            {
                $allowed = true;
            }
            
            if (! $allowed)
            {
                $user_groups = $user->get_groups();
                
                while ($user_group = $user_groups->next_result())
                {
                    if (in_array($user_group->get_id(), $pub->get_target_groups()))
                    {
                        $allowed = true;
                        break;
                    }
                }
            }
            
            if (! $allowed)
            {
                return false;
            }
        }
        
        if ($pub->is_hidden())
        {
            return false;
        }
        
        $time = time();
        
        if ($time < $pub->get_from_date() || $time > $pub->get_to_date())
        {
            return false;
        }
        
        return true;
	}
}

?>