<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Breadcrumb;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../../registration_viewer/registration_display.class.php';

class PackageManagerViewerComponent extends PackageManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(PackageManager :: PARAM_REGISTRATION);
        $this->registration = $this->get_parent()->retrieve_registration($id);

        $registration_display = RegistrationDisplay :: factory($this);

        $this->display_header();
        echo ($registration_display->as_html());
        $this->display_footer();
    }

    function get_registration()
    {
        return $this->registration;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManagerBrowserComponent')));
        $breadcrumbtrail->add_help('admin_package_manager_viewer');
    }

    function get_additional_parameters()
    {
        return array(PackageManager :: PARAM_REGISTRATION);
    }
}
?>