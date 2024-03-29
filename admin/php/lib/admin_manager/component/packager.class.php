<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\DelegateComponent;
/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 */
class AdminManagerPackagerComponent extends AdminManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
//        {
//            $this->display_header();
//            $this->display_error_message(Translation :: get('NotAllowed', array(), Utilities :: COMMON_LIBRARIES));
//            $this->display_footer();
//            exit();
//        }

        PackageManager :: launch($this);
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_package_manager');
    }
}
?>