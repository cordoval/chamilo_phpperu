<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

require_once dirname(__FILE__) . '/activity_changer.class.php';

class TrackingManagerEventDeactivatorComponent extends TrackingManagerActivityChangerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_TYPE, 'event');
        Request :: set_get(self :: PARAM_EXTRA, 'disable');
        parent :: run();
    }

}
?>