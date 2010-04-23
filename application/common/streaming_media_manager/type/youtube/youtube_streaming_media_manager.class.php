<?php
class YoutubeStreamingMediaManager extends StreamingMediaManager
{
	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'streaming_media_manager/type/youtube/component/';
	}
}
?>