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
 * @author 
 */
class CdaManagerVariableCreatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE)), Translation :: get('BrowseCda')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES)), Translation :: get('BrowseVariables')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateVariable')));

		$variable = new Variable();
		$form = new VariableForm(VariableForm :: TYPE_CREATE, $variable, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_variable();
			$this->redirect($success ? Translation :: get('VariableCreated') : Translation :: get('VariableNotCreated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES));
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