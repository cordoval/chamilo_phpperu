<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_cell_renderer.class.php';

class FlickrExternalRepositoryGalleryTableCellRenderer extends DefaultExternalRepositoryGalleryObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function FlickrExternalRepositoryGalleryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    function render_cell($object)
    {
        $html = array();
        $html[] = '<div style="width: 20px; float: right;">';
        $html[] = $this->get_modification_links($object);
        $html[] = '</div>';
        $html[] = $this->get_cell_content($object);
        return implode("\n", $html);
    }

    function get_cell_content($object)
    {
        $html = array();
        $display = ExternalRepositoryObjectDisplay :: factory($object);
        $html[] = '<h4>' . Utilities :: truncate_string($object->get_title(), 25) . '</h4>';
        $html[] = '<a href="' . $this->browser->get_external_repository_object_viewing_url($object) . '">' . $display->get_preview(true) . '</a>';

        if ($object->get_description())
        {
            $html[] = '<br/>';
            $html[] = '<i>' . Utilities :: truncate_string($object->get_description(), 100) . '</i>';
            $html[] = '<br/>';
        }

        return implode("\n", $html);
    }

    function get_modification_links($object)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        return $toolbar->as_html();
    }
}
?>