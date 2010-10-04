<?php
require_once dirname(__FILE__).'/channel.class.php';

class CombinedChannel extends Channel
{
	function combine_channels($channels = array())
	{
		foreach ($channels as $channel)
		{
			$this->add_items($channel->get_items());
		}
	}
	
	function combine_stream_channels($streams = array())
	{
		$channels = array();
		foreach ($streams as $stream)
		{
			$channels[] = $stream->get_channel();
		}
		$this->combine_channels($channels);
	}
}
?>