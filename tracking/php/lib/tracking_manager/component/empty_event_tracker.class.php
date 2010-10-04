<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

require_once dirname(__FILE__) . '/empty_tracker.class.php';

class TrackingManagerEmptyEventTrackerComponent extends TrackingManagerEmptyTrackerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_TYPE, 'event');
        parent :: run();
    }

}
?>