<?php
require_once dirname(__FILE__) . '/../streaming_media_object_display.class.php';
class StreamingMediaViewerComponent extends StreamingMediaComponent
{
    function run()
    {
        $this->display_header();
        
        $id = Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID);
        $object = $this->retrieve_streaming_media_object($id);
      	$display = StreamingMediaObjectDisplay::factory($object);
      	
        $html = array(); 
        $html[] = $display->as_html();
        
        $toolbar = new Toolbar();
        $toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
        $toolbar->add_item($toolbar_item);
        
        if ($this->get_parent()->is_editable($id))
        {
            $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_EDIT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
            $toolbar->add_item($toolbar_item_edit);
            
        	$toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_DELETE_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
        	$toolbar->add_item($toolbar_item_delete);
        }
        
        if ($this->get_parent()->is_stand_alone())
        {
            $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_SELECT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
            $toolbar->add_item($toolbar_item_select);
        }
        else
        {
            $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_IMPORT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
            $toolbar->add_item($toolbar_item_select);       
        }
        
        $html[] = '<br/>' . $toolbar->as_html();
        echo (implode("\n", $html));
        
        $this->display_footer();
    
    }
}
?>