<?php
/**
 * $Id: browser.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/phrases_publication_browser/phrases_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../../../../phrases_publication_menu.class.php';
require_once dirname(__FILE__) . '/../../../../forms/phrases_publication_filter_form.class.php';
/**
 * Admin component
 */
class PhrasesPublicationManagerBrowserComponent extends PhrasesPublicationManager
{
    private $action_bar;

    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        //        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdministration')));
        //        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        //        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PackageManager')));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('InstalledPackageList')));
        //        $trail->add_help('administration install');


        $this->action_bar = $this->get_action_bar();
        $this->form = new PhrasesPublicationFilterForm($this, $this->get_url());

        $this->display_header();

        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';
//        echo '<div id="phrases_tree_container" style="float: left; width: 15%;">';
//        echo $this->get_menu();
//        echo '</div>';
//        echo '<div style="float: right; width: 82%;">';
        echo $this->form->display();
        echo $this->get_table();
//        echo '</div>';

        $this->display_footer();
    }

    function get_table()
    {
        // array(Application :: PARAM_APPLICATION => PhrasesManager :: PARAM_APPLICATION, Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES)
        $table = new PhrasesPublicationBrowserTable($this, $this->get_table_parameters(), $this->get_condition());
        return $table->as_html();
    }

    function get_menu()
    {
        $menu = new PhrasesPublicationMenu($this->get_user());
        return $menu->render_as_tree();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(PhrasesPublicationManager :: PARAM_PUBLICATION_MANAGER_ACTION => PhrasesPublicationManager :: ACTION_PUBLISH))));
        return $action_bar;
    }

    function get_condition()
    {
        return $this->form->get_filter_conditions();
    }

    function get_table_parameters()
    {
        $form_parameters = $this->form->get_filter_parameters();
        $parameters = $this->get_parameters();

        return array_merge($form_parameters, $parameters);
    }
}
?>