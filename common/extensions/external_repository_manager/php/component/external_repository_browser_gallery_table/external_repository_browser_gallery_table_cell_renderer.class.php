<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;
use common\libraries\Toolbar;

class ExternalRepositoryBrowserGalleryTableCellRenderer extends DefaultExternalRepositoryGalleryObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
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

    function get_cell_content(ExternalRepositoryObject $object)
    {
        $html = array();
        $display = ExternalRepositoryObjectDisplay :: factory($object);
        $html[] = '<h4>' . Utilities :: truncate_string($object->get_title(), 25) . '</h4>';
        $html[] = $display->get_preview(true);

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