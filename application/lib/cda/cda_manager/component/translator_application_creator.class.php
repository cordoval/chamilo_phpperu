<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/translator_application_form.class.php';

/**
 * Component to create a new variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerTranslatorApplicationCreatorComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb('#', Translation :: get('ApplyTranslator')));

		$form = new TranslatorApplicationForm($this->get_url());

		if($form->validate())
		{
			$success = $form->create_application();
			$this->redirect($success ? Translation :: get('TranslatorApplicationCreated') : Translation :: get('TranslatorApplicationNotCreated'), !$success, 
				array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES));
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