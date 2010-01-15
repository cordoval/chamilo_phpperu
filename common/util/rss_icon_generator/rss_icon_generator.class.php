<?php
require_once dirname(__FILE__).'/../../global.inc.php';

class RssIconGenerator 
{
	const TYPE_PUBLICATION = 'publication';
	
	static function generate_rss_icon($application, $type, $user = null)
	{
		$url = self :: generate_rss_url($application, $type, $user);
		return '<a href="'.$url.'"><img src="'.Path :: get(WEB_LAYOUT_PATH).'/aqua/images/common/feed-icon-28x28.png" alt="" /></a>';
	}
	
	static function generate_rss_url($application, $type, $user = null)
	{
		$path = self :: get_rss_path($application, $type);
		if ($path != '')
		{
			if ($user != null)
			{
				$path .= '?sid'.$user->get_security_token();
			}
			return $path;
		}
		else
		{
			return '';
		}
	}
	
	static function get_rss_path($application, $type)
	{
		if (WebApplication :: is_application($application))
		{
			$path = Path :: get_application_path().'lib/'. $application . '/';
			$path .= 'rss/'.$type.'_rss.php';
			if (file_exists($path))
			{
				return Path :: get(WEB_APP_PATH).'lib/'. $application . '/rss/'.$type.'_rss.php';
			}
		}
		return '';
	}

}

//echo RssIconGenerator :: generate_rss_icon('assessment', RssIconGenerator :: TYPE_PUBLICATION);

?>