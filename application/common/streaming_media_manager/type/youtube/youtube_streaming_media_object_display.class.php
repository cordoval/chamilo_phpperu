<?php
class YoutubeStreamingMediaObjectDisplay extends StreamingMediaObjectDisplay
{
	function get_video_player_as_html()
	{
		$object = $this->get_object();
		$html = array();
		$html[] = '<embed style="margin: 1em 0 1em 0;" height="344" width="425" type="application/x-shockwave-flash" src="' . $object->get_url() . '"></embed>';
		return implode("\n", $html);
	}
	
	function tags_as_html()
	{
		$tags = $this->get_object()->get_tags();
		return implode(" ", $tags);
	}
	
	function get_additional_properties()
	{
		$html[] = '<tr><td>' . Translation :: get('Category') . '</td><td>' . Translation :: get($this->get_object()->get_category()) . '</td></tr>';
		$html[] = '<tr><td>' . Translation :: get('Tags') . '</td><td>' . $this->tags_as_html() . '</td></tr>';
		return implode("\n", $html);
	}
}
?>