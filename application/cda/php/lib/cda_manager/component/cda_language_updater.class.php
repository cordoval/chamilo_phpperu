<?php

namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
/**
 * @package application.cda.cda.component
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'forms/cda_language_form.class.php';

/**
 * Component to edit an existing cda_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguageUpdaterComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
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
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_languages_updater');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)), Translation :: get('CdaManagerAdminCdaLanguagesBrowserComponent')));
    }
    
 	function get_additional_parameters()
    {
    	return array(self :: PARAM_CDA_LANGUAGE);
    }
}
?>