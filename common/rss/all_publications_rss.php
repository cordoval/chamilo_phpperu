<?php
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__).'/builders/basic_rss.class.php';
require_once dirname(__FILE__).'/builders/combined_rss.class.php';

require_once Path :: get_application_path().'/lib/assessment/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/weblcms/rss/publication_rss.class.php';


class AllPublicationsRSS extends CombinedRSS
{
	
	/*function build_rss()
	{
		$rss_stream = $this->get_stream();
		$engine = new RSSEngine();
		return $engine->render_rss_stream($rss_stream);
	}*/
	
	/*function add_item($rss_xml, $engine)
	{
		$this->get_engine()->add_rss_xml($rss_xml);
	}*/
	
	/*function get_stream()
	{
		$rss_stream = new RSSStream();
		//$pub_rss = new AlexiaPublicationRSS();
		$streams = array();
		$streams[] = new AssessmentPublicationRSS();
		$streams[] = new WeblcmsPublicationRSS();
		$rss_stream->get_channel()->combine_stream_channels($streams);
		//$pub_rss = new AssessmentPublicationRSS();
		//$rss_stream->get_channel()->add_items($pub_rss->get_rss_channel()->get_items());
		//$pub_rss = new WeblcmsPublicationRSS();
		//$rss_stream->get_channel()->add_items($pub_rss->get_rss_channel()->get_items());
		//print_r($xml);
		return $rss_stream;
	}*/
	
	function get_basic_rss_objects()
	{
		$streams = array();
		$streams[] = new AssessmentPublicationRSS();
		$streams[] = new WeblcmsPublicationRSS();
		return $streams;
	}
	
	function get_channel_title()
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
	}
	
}

$pubrss = new AllPublicationsRSS();
echo $pubrss->build_rss();
?>