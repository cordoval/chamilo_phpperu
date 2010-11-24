<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\ActionBarSearchForm;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

/**
 * $Id: browser.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser.class.php';
/**
 * Admin component
 */
class PackageManagerBrowserComponent extends PackageManager
{
    private $action_bar;

    private $current_type;

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

        $this->current_type = Request :: get(PackageManager :: PARAM_REGISTRATION_TYPE);
        $this->current_type = $this->current_type ? $this->current_type : Registration :: TYPE_APPLICATION;

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = RegistrationBrowser :: factory($this, $parameters, $this->get_condition());

        $this->display_header();
        echo $this->action_bar->as_html();

        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicVisualTabsRenderer($renderer_name, $table->as_html());
        foreach (Registration :: get_types() as $type)
        {
            $selected = ($type == $this->current_type ? true : false);

            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($type) . 'Packages'));
            $params = $this->get_parameters();
            $params[PackageManager :: PARAM_REGISTRATION_TYPE] = $type;
            $link = $this->get_url($params);

            $tabs->add_tab(new DynamicVisualTab($section, $label, Theme :: get_image_path() . 'place_mini_' . $type . '.png', $link, $selected));

        }

        echo $tabs->render();
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, $this->get_type());

        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(Registration :: PROPERTY_NAME, '*' . $query . '*');
        }

        return new AndCondition($conditions);
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

    function get_type()
    {
        return $this->current_type;
    }
}
?>