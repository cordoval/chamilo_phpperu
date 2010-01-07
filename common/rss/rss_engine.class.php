<?php
require_once Path :: get_user_path().'/lib/user_data_manager.class.php';

class RSSEngine
{
	private $items = array();
	
	private $rss_xml;
	
	function create_header()
	{
		$xml[] = '<?xml version="1.0" ?>';
		$xml[] = '<rss version="2.0">';
  		$xml[] = '<channel>';
    	$xml[] = '<title>Dokeos RSS Feed</title>'; 
    	$xml[] = '<link>.</link>';
    	$xml[] = '<description>.</description>';
		return implode($xml, '');
	}
	
	function add_rss_xml($xml)
	{
		$this->rss_xml .= $xml;
	}
	
	function create_footer()
	{
		$xml[] = '</channel>';
		$xml[] = '</rss>';
		return implode($xml, '');
	}
	
	function create_item($item_info)
	{
		$xml[] = '<title>'.$item_info['title'].'</title>'; 
    	$xml[] = '<link>'.$item_info['url'].'</link>';
    	$xml[] = '<description>'.$item_info['description'].'</description>';
    	return implode($xml, '');
	}
	
	function create_rss($headers = true)
	{
		if ($headers)
			$rss[] = $this->create_header();
		
		foreach ($this->items as $key => $value)
		{
			$rss[] = $this->create_item($value);
		}
		$rss[] = $this->rss_xml;
		
		if ($headers)
			$rss[] = $this->create_footer();
			
		return implode ($rss, '');
	}
	
	function add_item($title, $description, $url)
	{
		$item['title'] = $title;
		$item['description'] = $description;
		$item['url'] = $url;
		$this->items[] = $item;
	}
	
	function get_user()
	{
		if (isset($_GET['sid']))
		{
			$sec_id = $_GET['sid'];
			$user = UserDataManager :: get_instance()->retrieve_user_by_security_token($sec_id);
			return $user; 
		}
		else
			return -1;
	}
	
	
}
?>