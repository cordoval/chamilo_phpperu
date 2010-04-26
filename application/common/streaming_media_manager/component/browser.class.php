<?php
class StreamingMediaBrowserComponent extends StreamingMediaComponent
{
	function run()
	{
		$streaming_media_objects = $this->retrieve_streaming_media_objects();
		$this->display_header();
		foreach($streaming_media_objects as $streaming_media_object)
		{
			echo('<img src="' . $streaming_media_object->get_thumbnail().'"/> <br/>');
			
		}
		echo($this->count_streaming_media_objects());
		$this->display_footer();
	}
}
?>