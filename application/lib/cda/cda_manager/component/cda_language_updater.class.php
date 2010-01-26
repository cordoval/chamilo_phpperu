<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/cda_language_form.class.php';

/**
 * Component to edit an existing cda_language object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerCdaLanguageUpdaterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE)), Translation :: get('BrowseCda')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES)), Translation :: get('BrowseCdaLanguages')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCdaLanguage')));

		$cda_language = $this->retrieve_cda_language(Request :: get(CdaManager :: PARAM_CDA_LANGUAGE));
		$form = new CdaLanguageForm(CdaLanguageForm :: TYPE_EDIT, $cda_language, $this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $cda_language->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_cda_language();
			$this->redirect($success ? Translation :: get('CdaLanguageUpdated') : Translation :: get('CdaLanguageNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES));
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