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
			$publications[] = $pub;
		}
		return $publications;
	}
	
	/*function get_channel_title()
	{
		return 'Chamilo weblcms';
	}
	
	function get_channel_link()
	{
		return 'http://localhost';
	}
	
	function get_channel_description()
	{
		return 'Weblcms publications';
	}
	
	function get_channel_source()
	{
		return 'http://localhost';
	}*/
}

?>