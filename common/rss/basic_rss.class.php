<?php
require_once dirname(__FILE__).'/rss_engine.class.php';

abstract class BasicRSS
{
	private $engine;
	
	public function build_rss($headers = true)
	{
		//retrieve items -> foreach item, add to engine
		$user = $this->engine->get_user();
		if ($user != -1)
		{
			$items = $this->retrieve_items($user);
			foreach ($items as $i => $item)
			{
				$this->add_item($item, $this->engine);
			}
		}
		
		return $this->engine->create_rss($headers);
	}
	
	public function get_user()
	{
		return $this->engine->get_user();
	}
	
	public function get_engine()
	{
		return $this->engine;
	}
	
	public function BasicRSS()
	{
		$this->engine = new RSSEngine();
	}
	
	abstract function retrieve_items($user, $min_date = '');
	
	abstract function add_item($object, $engine);
	
	
}
?>