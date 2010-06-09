<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/cda_language_form.class.php';

/**
 * Component to edit an existing cda_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguageUpdaterComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)), Translation :: get('AdminBrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCdaLanguage')));

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');

   		if (!$can_edit)
   		{
   		    Display :: not_allowed();
   		}

		$cda_language = $this->retrieve_cda_language(Request :: get(CdaManager :: PARAM_CDA_LANGUAGE));
		$form = new CdaLanguageForm(CdaLanguageForm :: TYPE_EDIT, $cda_language, $this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $cda_language->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_cda_language();
			$this->redirect($success ? Translation :: get('CdaLanguageUpdated') : Translation :: get('CdaLanguageNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
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