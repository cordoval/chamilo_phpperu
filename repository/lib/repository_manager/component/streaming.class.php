<?php
class RepositoryManagerStreamingComponent extends RepositoryManager
{
	function run()
	{
		$type = Request :: get(StreamingMediaManager :: PARAM_TYPE);
		$this->set_parameter(StreamingMediaManager :: PARAM_TYPE, $type);
		$streaming_media_manager = StreamingMediaManager :: factory($type, $this);
		$streaming_media_manager->run();
	}
}
?>