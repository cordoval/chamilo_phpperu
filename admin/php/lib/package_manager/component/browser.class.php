<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\ActionBarSearchForm;

/**
 * $Id: browser.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table.class.php';
/**
 * Admin component
 */
class PackageManagerBrowserComponent extends PackageManager
{
    private $action_bar;

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

        $this->action_bar = $this->get_action_bar();

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new RegistrationBrowserTable($this, $parameters, $this->get_condition());

        $this->display_header();
        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';
        echo $table->as_html();
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $condition = new PatternMatchCondition(Registration :: PROPERTY_NAME, '*' . $query . '*');
        }
        else
        {
            $condition = null;
        }

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('InstallLocal'), Theme :: get_image_path() . 'action_install_local.png', $this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_LOCAL_PACKAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('InstallRemote'), Theme :: get_image_path() . 'action_install_remote.png', $this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_REMOTE_PACKAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('InstallArchive'), Theme :: get_image_path() . 'action_install_archive.png', $this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_ARCHIVE_PACKAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_package_manager_browser');
    }
}
?>