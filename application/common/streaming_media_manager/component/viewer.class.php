<?php
class StreamingMediaViewerComponent extends StreamingMediaComponent
{
	function run()
	{
		$this->display_header();
		
		$id = Request :: get(StreamingMediaManager::PARAM_STREAMING_MEDIA_ID);
		$object = $this->retrieve_streaming_media_object($id);
		
		$html = array();
		$html[] = '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) .')</h3>';
		$html[] = $object->get_description() . '<br/>';
		$html[] = '<embed style="margin: 1em 0 1em 0;" height="344" width="425" type="application/x-shockwave-flash" src="' . $object->get_url() . '"></embed>';
		

		$toolbar = new Toolbar();
		$toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme::get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
		$toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme::get_common_image_path() . 'action_publish.png', $this->get_url(array(StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager::ACTION_SELECT_STREAMING_MEDIA, StreamingMediaManager::PARAM_STREAMING_MEDIA_ID => $id)));
		$toolbar->add_item($toolbar_item);
		$toolbar->add_item($toolbar_item_select);
		$html[] = '<br/>' . $toolbar->as_html();
		echo(implode("\n", $html));
		
		$this->display_footer();
		
	}
}
?>