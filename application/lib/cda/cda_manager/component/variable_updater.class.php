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
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE)), Translation :: get('BrowseCda')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES)), Translation :: get('BrowseVariables')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateVariable')));

		$variable = $this->retrieve_variable(Request :: get(CdaManager :: PARAM_VARIABLE));
		$form = new VariableForm(VariableForm :: TYPE_EDIT, $variable, $this->get_url(array(CdaManager :: PARAM_VARIABLE => $variable->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable();
			$this->redirect($success ? Translation :: get('VariableUpdated') : Translation :: get('VariableNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES));
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