<?php
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__).'/builders/basic_rss.class.php';
require_once dirname(__FILE__).'/builders/combined_rss.class.php';

require_once Path :: get_application_path().'/lib/assessment/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/weblcms/rss/publication_rss.class.php';


class AllPublicationsRSS extends CombinedRSS
{
	function AllPublicationsRSS()
	{
		parent :: CombinedRSS('Chamilo publications', 'http://localhost', 'Chamilo publications', 'http://localhost');
	}
	
	function get_basic_rss_objects()
	{
		$streams = array();
		$streams[] = new AssessmentPublicationRSS();
		$streams[] = new WeblcmsPublicationRSS();
		return $streams;
	}
	
	/*function get_channel_title()
	{
		return 'Dokeos publications';
	}
	
	function get_channel_link()
	{
		return 'http://localhost';
	}
	
	function get_channel_description()
	{
		return 'Dokeos publications';
	}
	
	function get_channel_source()
	{
		return 'http://localhost';
	}*/
	
}

$pubrss = new AllPublicationsRSS();
echo $pubrss->build_rss();
?>