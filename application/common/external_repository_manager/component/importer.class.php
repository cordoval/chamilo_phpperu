<?php
class StreamingMediaImporterComponent extends StreamingMediaComponent
{
	function run()
	{
		$id = Request :: get(StreamingMediaManager::PARAM_STREAMING_MEDIA_ID);
		$object = $this->retrieve_streaming_media_object($id);
		
                $succes = $this->get_parent()->import_streaming_media_object($object);

                $params =array();
                //$params[StreamingMediaManager :: PARAM_TYPE] = '';
                $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = '';

                
		if($succes)
                {
                    $this->redirect(Translation :: get('Succes'), false, $params);
                }
                else
                {
                    $this->redirect(Translation :: get('Failed'), true, $params);
                }
	}
}
?>