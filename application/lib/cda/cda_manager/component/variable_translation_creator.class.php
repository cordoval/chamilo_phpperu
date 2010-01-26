<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/variable_translation_form.class.php';

/**
 * Component to create a new variable_translation object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationCreatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE)), Translation :: get('BrowseCda')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS)), Translation :: get('BrowseVariableTranslations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateVariableTranslation')));

		$variable_translation = new VariableTranslation();
		$form = new VariableTranslationForm(VariableTranslationForm :: TYPE_CREATE, $variable_translation, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_variable_translation();
			$this->redirect($success ? Translation :: get('VariableTranslationCreated') : Translation :: get('VariableTranslationNotCreated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS));
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