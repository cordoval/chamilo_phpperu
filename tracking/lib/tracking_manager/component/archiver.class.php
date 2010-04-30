<?php
/**
 * $Id: archiver.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */


/**
 * Tracking Manager Archiver component which allows the administrator to archive the trackers
 *
 * @author Sven Vanpoucke
 */
class TrackingManagerArchiverComponent extends TrackingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('tracking general');
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $wizard = new ArchiveWizard($this);
        $wizard->run();
    }
}
?>