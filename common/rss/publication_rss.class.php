<?php
require_once dirname(__FILE__).'/rss_engine.class.php';

class PublicationRSS
{
	private $engine;
	
	public function build_rss()
	{
		//retrieve items -> foreach item, add to engine
		$user = $this->engine->get_user();
		if ($user != -1)
		{
			print_r($user);
			$items = $this->retrieve_items($user);
			foreach ($items as $i => $item)
			{
				$engine->add_item($item->get_title(), $item->get_description(), 'http://nietn');
			}
		}
		
		return $this->engine->create_rss();
	}
	
	public function PublicationRSS()
	{
		$this->engine = new RSSEngine();
	}
	
	private function retrieve_items($user, $min_date = '')
	{
		
	} 
	
	private function retrieve_item()
	{
		
	}
}
?>