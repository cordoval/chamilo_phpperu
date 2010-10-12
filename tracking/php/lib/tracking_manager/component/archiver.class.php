<?php
namespace tracking;
/**
 * $Id: archiver.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */


/**
 * Tracking Manager Archiver component which allows the administrator to archive the trackers
 *
 * @author Sven Vanpoucke
 */
class TrackingManagerArchiverComponent extends TrackingManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $wizard = new ArchiveWizard($this);
        $wizard->run();
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('tracking_archiver');
    }
}
?>