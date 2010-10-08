<?php
require_once dirname(__FILE__).'/builders/basic_rss.class.php';

abstract class PublicationRSS extends BasicRSS
{
	function add_item($publication, $channel)
	{
		$co = $publication->get_content_object();
		if (!is_object($co))
		{
			$co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
		}
		$channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
	}
	
	abstract function get_url($pub);
	
	abstract function is_visible_for_user($user, $pub);
	
}
?>