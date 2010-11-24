<?php
namespace application\weblcms\tool\maintenance;

use common\libraries\Display;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting.component
 */
/**
 * @author Michael Kyndt
 */

class MaintenanceToolViewerComponent extends MaintenanceTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses maintenance');

        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $wizard = new MaintenanceWizard($this);
        $wizard->run();
    }
}
?>