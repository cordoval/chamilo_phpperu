<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_cell_renderer.class.php';

class DropIoExternalRepositoryGalleryTableCellRenderer extends StreamingMediaExternalRepositoryBrowserGalleryTableCellRenderer
{
    function get_cell_content(ExternalRepositoryObject $object)
    {
        $html = array();
        $html[] = '<h3>' . Utilities :: truncate_string($object->get_title(), 25) . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()/1000) . ')</h3>';
        $display = ExternalRepositoryObjectDisplay :: factory($object);
        
        $html[] = '<a href="' . $this->get_browser()->get_external_repository_object_viewing_url($object) . '">' . $display->get_preview(true) . '</a><br/>';
        $html[] = '<i>' . Utilities :: truncate_string($object->get_description(), 100) . '</i><br/>';
        return implode("\n", $html);
    }
}
?>