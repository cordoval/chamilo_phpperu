<?php
require_once dirname(__FILE__).'/builders/basic_rss.class.php';

abstract class PublicationRSS extends BasicRSS
{
	function add_item($publication, $channel)
	{
		$co = $publication->get_content_object();
		$channel->add_item($co->get_title(), 'http://nietn', $co->get_description());
	}
	
}
?>