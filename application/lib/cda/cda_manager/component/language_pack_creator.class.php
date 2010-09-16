<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/language_pack_form.class.php';

/**
 * Component to create a new language_pack object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerLanguagePackCreatorComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
	   	$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');

   		if (!$can_add)
   		{
   		    Display :: not_allowed();
   		}

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
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_language_pack_creator');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('CdaManagerAdminLanguagePacksBrowserComponent')));
    }
}
?>