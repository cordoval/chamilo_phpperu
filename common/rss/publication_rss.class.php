<?php
require_once dirname(__FILE__).'/basic_rss.class.php';

abstract class PublicationRSS extends BasicRSS
{
	function add_item($publication, $engine)
	{
		$co = $publication->get_content_object();
		$engine->add_item($co->get_title(), $co->get_description(), 'http://nietn');
	}
	
}
?>