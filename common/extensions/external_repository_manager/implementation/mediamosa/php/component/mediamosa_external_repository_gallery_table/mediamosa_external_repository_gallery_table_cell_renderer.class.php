<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;

use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTableCellRenderer;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\libraries\Utilities;
use common\libraries\Toolbar;

class MediamosaExternalRepositoryGalleryTableCellRenderer extends DefaultExternalRepositoryGalleryObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function MediamosaExternalRepositoryGalleryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    function get_cell_content(ExternalRepositoryObject $object)
    {
        $html = array();
        $html[] = '<h3>' . Utilities :: truncate_string($object->get_title(), 25) . '</h3>';
        $html[] = '<a href="' . $this->browser->get_external_repository_object_viewing_url($object) . '"><img class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . Utilities :: truncate_string($object->get_description(), 100) . '</i><br/>';
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