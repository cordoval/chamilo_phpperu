<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\libraries\Utilities;
use common\libraries\Toolbar;

use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTableCellRenderer;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

class PhotobucketExternalRepositoryGalleryTableCellRenderer extends DefaultExternalRepositoryGalleryObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PhotobucketExternalRepositoryGalleryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    function get_cell_content(ExternalRepositoryObject $object)
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
        $toolbar->add_items($this->browser->get_external_repository_object_actions($object));
        return $toolbar->as_html();
    }
}
?>