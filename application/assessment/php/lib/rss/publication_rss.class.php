<?php

namespace application\assessment;

use common\libraries\PublicationRSS;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\Redirect;




class AssessmentPublicationRSS extends PublicationRSS
{
	function __construct()
	{
		parent :: __construct('Chamilo assessments', htmlspecialchars(Path :: get(WEB_PATH)), 'Assessment publications', htmlspecialchars(Path :: get(WEB_PATH)));
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