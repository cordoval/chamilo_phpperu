<?php
require_once dirname(__FILE__) . '/default_object_publication_gallery_table_cell_renderer.class.php';

class ObjectPublicationGalleryTableCellRenderer extends DefaultObjectPublicationGalleryTableCellRenderer
{
    private $table_renderer;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ObjectPublicationGalleryTableCellRenderer($table_renderer)
    {
        parent :: __construct();
        $this->table_renderer = $table_renderer;
    }

    function render_cell($publication)
    {
        $object = $publication->get_content_object();
        
        $html = array();
        $html[] = '<div style="width:20px;float:right;">';
        $html[] = $this->get_modification_links($publication);
        $html[] = '</div>';
        $html[] = '<h3>' . Utilities :: truncate_string($object->get_title(), 25) . '</h3>';
        $display = ContentObjectDisplay :: factory($object);
        $html[] = $display->get_thumbnail();
        //        $html[] = '<a href="' . $this->browser->get_streaming_media_object_viewing_url($object) . '"><img class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        //        $html[] = '<i>' . Utilities :: truncate_string($object->get_description(), 100) . '</i><br/>';
        return implode("\n", $html);
    }

    function get_modification_links($publication)
    {
        $toolbar = $this->table_renderer->get_publication_actions($publication, false);
        $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
        
        //        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        //		$id = $object->get_id();
        //
        //        if ($this->browser->get_parent()->is_editable($id))
        //        {
        //            $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_EDIT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
        //            $toolbar->add_item($toolbar_item_edit);
        //
        //        	$toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_DELETE_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
        //        	$toolbar->add_item($toolbar_item_delete);
        //        }
        //
        //        if ($object->is_usable() && $object->get_url() != null){
        //	        if ($this->browser->get_parent()->is_stand_alone())
        //	        {
        //	            $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->browser->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_SELECT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
        //	            $toolbar->add_item($toolbar_item_select);
        //	        }
        //	        else
        //	        {
        //	            $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->browser->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => StreamingMediaManager :: ACTION_IMPORT_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
        //	            $toolbar->add_item($toolbar_item_select);
        //	        }
        //        }
        

        return $toolbar->as_html();
    }
}
?>