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
        $details_url = $this->table_renderer->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_VIEW));
        $display = ContentObjectDisplay :: factory($object);
        $html[] = '<a href="'. $details_url .'">' . $display->get_preview(true) . '</a>';
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