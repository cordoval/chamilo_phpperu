<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\Toolbar;

/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/video_conferencing_browser_table_column_model.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class VideoConferencingBrowserTableCellRenderer extends DefaultVideoConferencingObjectTableCellRenderer
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

    // Inherited
    function render_cell($column, $external_object)
    {
        if ($column === VideoConferencingBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($external_object);
        }

        switch ($column->get_name())
        {
            case VideoConferencingObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $external_object);
                $title_short = Utilities :: truncate_string($title, 50, false);
                return '<a href="' . htmlentities($this->browser->get_video_conferencing_object_viewing_url($external_object)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        return parent :: render_cell($column, $external_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($video_conferencing_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_video_conferencing_object_actions($video_conferencing_object));
        return $toolbar->as_html();
    }
}
?>