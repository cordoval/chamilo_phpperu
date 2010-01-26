<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/variable_form.class.php';

/**
 * Component to edit an existing variable object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableUpdaterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable = $this->retrieve_variable(Request :: get(CdaManager :: PARAM_VARIABLE));
		$language_pack_id = $variable->get_language_pack_id();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('BrowseVariables')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_VARIABLE => $variable->get_id())), Translation :: get('UpdateVariable')));

		$form = new VariableForm(VariableForm :: TYPE_EDIT, $variable, $this->get_url(array(CdaManager :: PARAM_VARIABLE => $variable->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable();
			$this->redirect($success ? Translation :: get('VariableUpdated') : Translation :: get('VariableNotUpdated'), !$success, 
						    array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>