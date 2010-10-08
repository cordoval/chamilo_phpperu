<?php
require_once dirname(__FILE__).'/combined_channel.class.php';

class RSSStream
{
	private $channel;
	
	function RSSStream()
	{
		$this->channel = new CombinedChannel();
	}
	
	function get_channel()
	{
		return $this->channel;
	} 
	
	function set_channel($channel)
	{
		$this->channel = $channel;
	}
}

?>