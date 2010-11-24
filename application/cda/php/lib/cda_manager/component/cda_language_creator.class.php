<?php

namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;

/**
 * @package application.cda.cda.component
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'forms/cda_language_form.class.php';

/**
 * Component to create a new cda_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguageCreatorComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
   		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');

   		if (!$can_add)
   		{
   		    Display :: not_allowed();
   		}

		$cda_language = new CdaLanguage();
		$form = new CdaLanguageForm(CdaLanguageForm :: TYPE_CREATE, $cda_language, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_cda_language();
                        $object = Translation :: get('Language');
                        $message = $success ? Translation :: get('ObjectCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                              Translation :: get('ObjectNotCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

			$this->redirect($message, !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
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
    	$breadcrumbtrail->add_help('cda_admin_languages_creator');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)), Translation :: get('CdaManagerAdminCdaLanguagesBrowserComponent')));
    }
	
}
?>