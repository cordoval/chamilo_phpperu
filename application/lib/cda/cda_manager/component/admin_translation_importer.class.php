<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/translation_import_form.class.php';
require_once dirname(__FILE__) . '/translation_importer/translation_importer.class.php';

/**
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerAdminTranslationImporterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => CdaManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Cda') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ImportTranslations')));

		$form = new TranslationImportForm($this, $this->get_url());

		if($form->validate())
		{
			$branch = $form->exportValue(LanguagePack :: PROPERTY_BRANCH);
			$file = Request :: file('file');
			
			$options = array(TranslationImporter :: OPTION_CREATE_NEW_LANGUAGE_PACKS => 0, 
							 TranslationImporter :: OPTION_CREATE_NEW_LANGUAGES => 0,
							 TranslationImporter :: OPTION_CREATE_NEW_VARIABLES => 0);
			
			$importer = TranslationImporter :: factory($branch, $this->get_user(), $options);
			$importer->import($file);
			//$this->redirect($success ? Translation :: get('CdaLanguageCreated') : Translation :: get('CdaLanguageNotCreated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
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