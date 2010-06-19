<?php
class ObjectPublicationGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{
    private $table_renderer;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ObjectPublicationGalleryTableCellRenderer($table_renderer)
    {
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
        return $toolbar->as_html();
    }
    
    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>