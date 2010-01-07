<?php
require_once dirname(__FILE__).'/../rss_engine.class.php';
require_once dirname(__FILE__).'/../objects/rss_stream.class.php';
require_once dirname(__FILE__).'/../objects/channel.class.php';

abstract class BasicRSS
{
	private $rss_stream;
	
	function build_rss()
	{
		$engine = new RSSEngine();
		return $engine->render_rss_stream($this->get_rss_stream());
	}
	
	function get_rss_stream()
	{
		return $this->rss_stream;
	}
	
	function set_rss_stream($stream)
	{
		$this->rss_stream = $stream;
	}
	
	function get_rss_channel()
	{
		$channel = $this->rss_stream->get_channel();
		return $channel;
	}
	
	function add_items($user)
	{
		$items = $this->retrieve_items($user);
		$channel = $this->rss_stream->get_channel();
		
		foreach ($items as $i => $item)
		{
			$this->add_item($item, $channel);
		}
		//$this->rss_stream->add_channel($channel);
	}
	
	function set_channel_properties()
	{
		$channel = $this->rss_stream->get_channel();
		$channel->set_title($this->get_channel_title());
		$channel->set_link($this->get_channel_link());
		$channel->set_description($this->get_channel_description());
		$channel->set_source($this->get_channel_source());
	}
	
	function get_user()
	{
		return RSSEngine :: get_user();
	}
	
	function BasicRSS()
	{
		$this->rss_stream = new RSSStream();
		$this->set_channel_properties();
		$this->get_data();
	}
	
	function get_data()
	{
		$user = RSSEngine :: get_user();
		if ($user != -1)
		{
			$this->add_items($user);
		}
	}
	
	abstract function retrieve_items($user);
	
	abstract function add_item($object, $channel);
	
	abstract function get_channel_title();
	abstract function get_channel_link();
	abstract function get_channel_description();
	abstract function get_channel_source();
	
}
?>