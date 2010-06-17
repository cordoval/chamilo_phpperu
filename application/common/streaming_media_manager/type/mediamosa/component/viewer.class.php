<?php
/**
 * Description of viewerclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../../../streaming_media_object_display.class.php';

class MediamosaStreamingMediaManagerViewerComponent extends MediamosaStreamingMediaManager
{
	function run()
	{
            
            $viewer = StreamingMediaComponent::factory(StreamingMediaComponent::VIEWER_COMPONENT, $this);
                /*$viewer->run();*/
            $viewer->display_header();

            $id = Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID);
            $object = $viewer->retrieve_streaming_media_object($id);
            $display = StreamingMediaObjectDisplay::factory($object);

            $html = array();
            $html[] = $display->as_html($viewer);

            $toolbar = new Toolbar();
            $toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
            $toolbar->add_item($toolbar_item);

            if ($viewer->get_parent()->is_editable($id))
            {
                $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $viewer->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_EDIT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
                $toolbar->add_item($toolbar_item_edit);

                    $toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $viewer->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_DELETE_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
                    $toolbar->add_item($toolbar_item_delete);
            }

            if ($object->is_usable($id))
            {
                
                if ($viewer->get_parent()->is_stand_alone())
                    {
                        $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $viewer->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_SELECT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
                        $toolbar->add_item($toolbar_item_select);
                    }
                    else
                    {
                        $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $viewer->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_IMPORT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)));
                        $toolbar->add_item($toolbar_item_select);
                    }
            }

            $html[] = '<br/>' . $toolbar->as_html();
            echo (implode("\n", $html));

            $viewer->display_footer();
	}
}
?>
