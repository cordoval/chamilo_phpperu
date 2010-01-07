<?php
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../data_manager/database.class.php';

class AssessmentPublicationRSS extends PublicationRSS
{
	function retrieve_items($user, $min_date = '')
	{
		$pubs = AssessmentDataManager :: get_instance()->retrieve_assessment_publications(null, null, 20);//, array('id', SORT_DESC));
		$publications = array();
		while ($pub = $pubs->next_result())
		{
			$publications[] = $pub;
		}
		return $publications;
	}
}

?>