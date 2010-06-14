<?php
/**
 * $Id: browser.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/phrases_mastery_level_browser/phrases_mastery_level_browser_table.class.php';
/**
 * Admin component
 */
class PhrasesMasteryLevelManagerBrowserComponent extends PhrasesMasteryLevelManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        ;
        //        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        //        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        //        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PackageManager')));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('InstalledPackageList')));
        //        $trail->add_help('administration install');


        $this->action_bar = $this->get_action_bar();

        //        $parameters = $this->get_parameters();
        //        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        //        $table = new RegistrationBrowserTable($this, $parameters, $this->get_condition());


        $this->display_header();
        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';
        echo $this->get_table();
        $this->display_footer();
    }

    function get_table()
    {
        $table = new PhrasesMasteryLevelBrowserTable($this, array(Application :: PARAM_APPLICATION => PhrasesManager :: PARAM_APPLICATION, Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES));
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_CREATE))));
        return $action_bar;
    }
}
?>