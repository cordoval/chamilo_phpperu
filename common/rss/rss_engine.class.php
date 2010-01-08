<?php
require_once Path :: get_user_path().'/lib/user_data_manager.class.php';

class RSSEngine
{
	function render_channel($channel)
	{
		$xml[] = $this->create_channel_header($channel);
		
		foreach ($channel->get_items() as $item)
		{
			$xml[] = $this->render_item($item);
		}
		
		$xml[] = '</channel>';
		return implode($xml, '');
	}
	
	function render_rss_stream($stream)
	{
		$xml[] = $this->create_header();
		$xml[] = $this->render_channel($stream->get_channel());
		$xml[] = '</rss>';
		return implode($xml, '');
	}
	
	function create_header()
	{
		$xml[] = '<?xml version="1.0" ?>';
		$xml[] = '<rss version="2.0">';
		return implode($xml, '');
	}
	
	function create_channel_header($channel)
	{
		$xml[] = '<channel>';
    	$xml[] = '<title>'.$channel->get_title().'</title>'; 
    	$xml[] = '<link>'.$channel->get_link().'</link>';
    	$xml[] = '<description>'.$channel->get_description().'</description>';
    	$xml[] = '<source>'.$channel->get_source().'</source>';
    	return implode($xml, '');
	}
	
	function render_item($item_info)
	{
		$xml[] = '<item>';
		$xml[] = '<title>'.$item_info['title'].'</title>'; 
    	$xml[] = '<link>'.$item_info['link'].'</link>';
    	$xml[] = '<description>'.$item_info['description'].'</description>';
    	$xml[] = '</item>';
    	return implode($xml, '');
	}
	
	static function get_user()
	{
		if (Request :: get('sid') != '')
		{
			$sec_id = Request :: get('sid');
			$user = UserDataManager :: get_instance()->retrieve_user_by_security_token($sec_id);
			return $user; 
		}
		else
			return -1;
	}
	
	
}
?>