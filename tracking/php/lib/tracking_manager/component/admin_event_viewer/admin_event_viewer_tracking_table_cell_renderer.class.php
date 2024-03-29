<?php
namespace tracking;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Utilities;

/**
 * $Id: admin_event_viewer_tracking_table_cell_renderer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.admin_event_viewer
 */

/**
 * Class used to retrieve the modification links for the admin events viewer tables
 */
class AdminEventViewerTrackingTableCellRenderer
{
    /**
     * Eventviewer where this cellrenderer belongs to
     */
    private $eventviewer;
    private $event;

    /**
     * Constructor
     * @param AdminTrackingBrowser $browser The browser where this renderer belongs to
     */
    function __construct($eventviewer, $event)
    {
        $this->eventviewer = $eventviewer;
        $this->event = $event;
    }

    /**
     * Creates the modification links for the given tracker
     * @param Tracker $tracker the tracker
     * @return string The modification links for the given tracker
     */
    function get_modification_links($tracker)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(($tracker->get_active() == 1) ? Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES) : Translation :: get('Visible', null, Utilities :: COMMON_LIBRARIES), ($tracker->get_active() == 1) ? (Theme :: get_common_image_path() . 'action_visible.png') : (Theme :: get_common_image_path() . 'action_invisible.png'), $this->eventviewer->get_change_active_url('tracker', $this->event->get_id(), $tracker->get_id()), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Empty_Tracker'), Theme :: get_common_image_path() . 'action_delete.png', $this->eventviewer->get_empty_tracker_url($this->event->get_id(), $tracker->get_id()), ToolbarItem :: DISPLAY_ICON, true));
        return $toolbar->as_html();

    }

    /**
     * Renders a cell
     * @param string $property the property name
     * @param Tracker $tracker the tracker
     */
    function render_cell($property, $tracker)
    {
        switch ($property)
        {
            /*case Event :: PROPERTY_NAME: return '<a href="' .
				$this->browser->get_event_viewer_url($event) . '">' .
				$event->get_default_property($property) . '</a>';*/
        }

        return $tracker->get_default_property($property);
    }

    /**
     * Returns the properties that will become the columns
     * @return array of properties
     */
    function get_properties()
    {
        return array(TrackerRegistration :: PROPERTY_ID, TrackerRegistration :: PROPERTY_TRACKER, TrackerRegistration :: PROPERTY_APPLICATION);
    }
}
?>