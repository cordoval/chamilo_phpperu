<?php
class StreamingMediaImporterComponent extends StreamingMediaComponent
{
	function run()
	{
		$id = Request :: get(StreamingMediaManager::PARAM_STREAMING_MEDIA_ID);
		$object = $this->retrieve_streaming_media_object($id);
		
		$this->get_parent()->import_streaming_media_object($object);
				
	}
}
?>