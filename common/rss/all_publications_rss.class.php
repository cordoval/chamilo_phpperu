<?php
require_once dirname(__FILE__).'/basic_rss.class.php';
//require_once Path :: get_application_path().'/lib/alexia/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/assessment/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/weblcms/rss/publication_rss.class.php';


class AllPublicationsRSS extends BasicRSS
{
	function add_item($rss_xml, $engine)
	{
		$this->get_engine()->add_rss_xml($rss_xml);
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$xml = array();
		//$pub_rss = new AlexiaPublicationRSS();
		$pub_rss = new AssessmentPublicationRSS();
		$xml[] = $pub_rss->build_rss(false);
		$pub_rss = new WeblcmsPublicationRSS();
		$xml[] = $pub_rss->build_rss(false);
		
		
		return $xml;
	}
	
}
?>