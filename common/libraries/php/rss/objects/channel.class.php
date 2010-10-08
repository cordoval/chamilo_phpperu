<?php

class Channel
{
	private $title;
	private $link;
	private $description;
	private $source;
	
	private $items;
	
	function Channel()
	{
		$items = array();
	}
	
	function get_items()
	{
		return $this->items;
	}
	
	function set_title($title)
	{
		$this->title = $title;
	}
	
	function get_title()
	{
		return $this->title;
	}
	
	function set_link($link)
	{
		$this->link = $link;
	}
	
	function get_link()
	{
		return $this->link;
	}
	
	function set_description($description)
	{
		$this->description = $description;
	}
	
	function get_description()
	{
		return $this->description;
	}
	
	function get_source()
	{
		return $this->source;
	}
	
	function set_source($source)
	{
		$this->source = $source;
	}
	
	function add_item($title, $link, $description)
	{
		$this->items[] = array('title' => $title, 'link' => $link, 'description' => $description);
	}
	
	function add_items($items)
	{
		foreach ($items as $item)
		{
			$this->items[] = $item;
		}
	}
}
?>