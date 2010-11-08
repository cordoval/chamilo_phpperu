<?php

namespace application\cda;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\Utilities;
/**
 * @package application.cda.cda.component
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'forms/translation_import_form.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/translation_importer/translation_importer.class.php';

/**
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerTranslationImporterComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
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

			$this->redirect(Translation :: get('ObjectImported', array('OBJECT' => Translation :: get('Translations')), Utilities :: COMMON_LIBRARIES), false, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES));
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
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('cda_languages_importer');
    }
}
?>