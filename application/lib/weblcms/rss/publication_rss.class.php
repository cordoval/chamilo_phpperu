<?php
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';

class WeblcmsPublicationRSS extends PublicationRSS
{
	function WeblcmsPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo weblcms', htmlspecialchars(Path :: get(WEB_PATH)), 'Weblcms publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($this->get_access_condition($user), new ObjectTableOrder(ContentObjectPublication :: PROPERTY_PUBLICATION_DATE, SORT_DESC), 0, 20);//, array('id', SORT_DESC));
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
		$co = $publication->get_content_object();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		
		$title = Translation :: get(Utilities :: underscores_to_camelcase($publication->get_tool())) . ': ' . htmlspecialchars($co->get_title());
		
		$channel->add_item($title, htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
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
	
	private function get_access_condition($user)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		if ($user->is_platform_admin())
        {
            $user_id = array();
            $course_group_ids = array();
        }
        else
        {
            $user_id = $user->get_id();
            $course_groups = $this->get_user_groups();
                
            $course_group_ids = array();
                
            foreach($course_groups as $course_group)
            {
                $course_group_ids[] = $course_group->get_id();
            }
        }
            
        $access = array();
        
        if(!empty($user_id))
        {
        	$access[] = new InCondition('user_id', $user_id, $wdm->get_database()->get_alias('content_object_publication_user'));
        }
        
        if(!empty($course_group_ids))
        {
        	$access[] = new InCondition('course_group_id', $course_group_ids, $wdm->get_database()->get_alias('content_object_publication_course_group'));
        }
        
        if (! empty($user_id) || ! empty($course_groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $wdm->get_database()->get_alias('content_object_publication_user')), 
            	new EqualityCondition('course_group_id', null, $wdm->get_database()->get_alias('content_object_publication_course_group'))));
        }
        
        if(!empty($access))
        {
        	return new OrCondition($access);
        }
	}
	
	private function get_user_groups($user)
	{
		$wdm = WeblcmsDataManager :: get_instance();
        return $wdm->get_user_course_groups($user);
	}
}

?>