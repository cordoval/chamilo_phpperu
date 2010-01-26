<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/cda_language_form.class.php';

/**
 * Component to create a new cda_language object
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerCdaLanguageCreatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)), Translation :: get('AdminBrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateCdaLanguage')));

		$cda_language = new CdaLanguage();
		$form = new CdaLanguageForm(CdaLanguageForm :: TYPE_CREATE, $cda_language, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_cda_language();
			$this->redirect($success ? Translation :: get('CdaLanguageCreated') : Translation :: get('CdaLanguageNotCreated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
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