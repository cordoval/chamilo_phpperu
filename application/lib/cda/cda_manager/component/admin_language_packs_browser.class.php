<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/language_pack_browser/language_pack_browser_table.class.php';
require_once dirname(__FILE__) . '/../../forms/language_pack_browser_filter_form.class.php';

/**
 * cda component which allows the user to browse his language_packs
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerAdminLanguagePacksBrowserComponent extends CdaManagerComponent
{
	private $actionbar;
	private $form;
	
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseLanguagePacks')));
		$this->actionbar = $this->get_action_bar();
		
		$this->display_header($trail);

		echo $this->actionbar->as_html();
		echo $this->get_table();
		
		$this->display_footer();
	}

	function get_table()
	{
		$this->form = new LanguagePackBrowserFilterForm($this, $this->get_url());
		$table = new LanguagePackBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS), $this->get_condition());
		
		$html[] = $this->form->display();
        $html[] = $table->as_html();
        return implode("\n", $html);
	}
	
    function get_condition()
    {
        $form = $this->form;

        return $form->get_filter_conditions();
    }

	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddLanguagePack'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_language_pack_url()));
        
        return $action_bar;
    }
	
    function get_cda_language()
    {
    	return null;
    }
}
?>