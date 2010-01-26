<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/variable_translation_form.class.php';

/**
 * Component to edit an existing variable_translation object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationUpdaterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS)), Translation :: get('BrowseVariableTranslations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateVariableTranslation')));

		$variable_translation = $this->retrieve_variable_translation(Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION));
		$form = new VariableTranslationForm(VariableTranslationForm :: TYPE_EDIT, $variable_translation, $this->get_url(array(CdaManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable_translation();
			$this->redirect($success ? Translation :: get('VariableTranslationUpdated') : Translation :: get('VariableTranslationNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS));
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