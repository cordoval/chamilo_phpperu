<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/variable_form.class.php';

/**
 * Component to create a new variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableCreatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('BrowseVariables')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('CreateVariable')));

	   	$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, 'variables', 'manager');

   		if (!$can_add)
   		{
   		    Display :: not_allowed();
   		}


		$variable = new Variable();
		$variable->set_language_pack_id($language_pack_id);
		$form = new VariableForm(VariableForm :: TYPE_CREATE, $variable, $this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_variable();
			$this->redirect($success ? Translation :: get('VariableCreated') : Translation :: get('VariableNotCreated'), !$success,
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