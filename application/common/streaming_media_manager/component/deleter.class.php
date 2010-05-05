<?php
class StreamingMediaDeleterComponent extends StreamingMediaComponent
{
	
	function run ()
	{
		$id = Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID);
		$object = $this->delete_streaming_media_object($id);
	}
}
?>
