<?php
require_once dirname(__FILE__).'/basic_rss.class.php';

abstract class CombinedRSS extends BasicRSS
{
	function CombinedRSS($title, $link, $description, $source = '')
	{
		$this->set_rss_stream(new RSSStream());
		$this->set_channel_properties($title, $link, $description, $source);
		$basic_rss_objs = $this->get_basic_rss_objects();
		$this->combine_basic_rss($basic_rss_objs);
	}
	
	function combine_basic_rss($basic_rss_objs = array())
	{
		$streams = array();
		foreach ($basic_rss_objs as $basic_rss)
		{
			$streams[] = $basic_rss->get_rss_stream();
		}
		$this->get_rss_stream()->get_channel()->combine_stream_channels($streams);
	}	
	
	abstract function get_basic_rss_objects();
	
	function add_item($object, $channel)
	{
		
	}
	
	function retrieve_items($user)
	{
		
	}
}


?>