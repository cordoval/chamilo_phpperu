<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/language_pack_form.class.php';

/**
 * Component to edit an existing language_pack object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerLanguagePackUpdaterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLanguagePack')));

		$language_pack = $this->retrieve_language_pack(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));
		$form = new LanguagePackForm(LanguagePackForm :: TYPE_EDIT, $language_pack, $this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_language_pack();
			$this->redirect($success ? Translation :: get('LanguagePackUpdated') : Translation :: get('LanguagePackNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
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