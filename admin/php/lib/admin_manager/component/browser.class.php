<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ResourceManager;
use common\libraries\EqualityCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicAction;
use common\libraries\DynamicActionsTab;
use common\libraries\Theme;
/**
 * $Id: browser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class AdminManagerBrowserComponent extends AdminManager
{
	const PARAM_TAB = 'tab';
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//    	if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
//        {
//            $this->display_header();
//            $this->display_error_message(Translation :: get('NotAllowed'));
//            $this->display_footer();
//            exit();
//        } 
        
        $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        $breadcrumbtrail->truncate(true);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('Administration', null, 'admin')));
        
        $tab = Request :: get(self :: PARAM_TAB);
        if(!$tab)
        {
        	$tab = 'admin';
        }
        $tab_name = Translation :: get(Utilities :: underscores_to_camelcase($tab));
        
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab)), $tab_name));
        
        $links = $this->get_application_platform_admin_links();
        $this->display_header();
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/admin_browser.js');
        echo $this->get_application_platform_admin_tabs($links);
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_application_platform_admin_tabs($links)
    {
    	$tabs = new DynamicTabsRenderer('admin');
        
        $index = 0;
        foreach ($links as $application_links)
        {
            if (count($application_links['links']))
            {
                $index ++;
                $html = array();
                $actions_tab = new DynamicActionsTab($application_links['application']['class'], Translation :: get($application_links['application']['name']), Theme :: get_image_path('admin') . 'place_mini_' . $application_links['application']['class'] . '.png', implode("\n", $html));
                
                if (isset($application_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $application_links['search'], $index);
                    $actions_tab->add_action(new DynamicAction(null, $search_form->display(), Theme :: get_image_path() . 'browse_search.png'));
                }
                
                $condition = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application_links['application']['class']);
                $application_settings_count = AdminDataManager :: get_instance()->count_settings($condition);
                
                if ($application_settings_count)
                {
                    $settings_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PLATFORM, self :: PARAM_WEB_APPLICATION => $application_links['application']['class']));
                    $actions_tab->add_action(new DynamicAction(Translation :: get('Settings'), Translation :: get('SettingsDescription'), Theme :: get_image_path() . 'browse_manage.png', $settings_url));
                }
                
                foreach ($application_links['links'] as $action)
                {
                    $actions_tab->add_action($action);
                }
                
                $tabs->add_tab($actions_tab);
            }
        }
        
        return $tabs->render();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_browser');
    }
}
?>