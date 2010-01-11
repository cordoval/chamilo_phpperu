<?php
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';
require_once dirname(__FILE__).'/../assessment_manager/assessment_manager.class.php';

class AssessmentPublicationRSS extends PublicationRSS
{
	function AssessmentPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo assessments', 'http://localhost', 'Assessment publications', 'http://localhost');
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = AssessmentDataManager :: get_instance()->retrieve_assessment_publications(null, null, 20);//, array('id', SORT_DESC));
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
		$params[Application :: PARAM_ACTION] = AssessmentManager :: ACTION_VIEW_ASSESSMENT_PUBLICATION;
		$params[AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION] = $pub->get_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(AssessmentManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->is_visible_for_target_user($user);
	}
}

?>