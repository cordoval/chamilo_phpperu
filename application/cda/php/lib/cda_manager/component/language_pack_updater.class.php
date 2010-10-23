<?php

namespace application\cda;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\WebApplication;
/**
 * @package application.cda.cda.component
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'forms/language_pack_form.class.php';

/**
 * Component to edit an existing language_pack object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerLanguagePackUpdaterComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');

   		if (!$can_edit)
   		{
   		    Display :: not_allowed();
   		}

		$language_pack = $this->retrieve_language_pack(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));
		$form = new LanguagePackForm(LanguagePackForm :: TYPE_EDIT, $language_pack, $this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_language_pack();
			$this->redirect($success ? Translation :: get('LanguagePackUpdated') : Translation :: get('LanguagePackNotUpdated'), !$success, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
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
    	$breadcrumbtrail->add_help('cda_admin_language_pack_updater');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('CdaManagerAdminLanguagePacksBrowserComponent')));
    }
	
 	function get_additional_parameters()
    {
    	return array(self :: PARAM_LANGUAGE_PACK);
    }
}
?>