<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/language_pack_form.class.php';

/**
 * Component to create a new language_pack object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerLanguagePackCreatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLanguagePack')));

		$language_pack = new LanguagePack();
		$form = new LanguagePackForm(LanguagePackForm :: TYPE_CREATE, $language_pack, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_language_pack();
			$this->redirect($success ? Translation :: get('LanguagePackCreated') : Translation :: get('LanguagePackNotCreated'), !$success, 
					array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
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