<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\AdministrationComponent;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */
/**
 * Component to edit an existing variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableUpdaterComponent extends PackageManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable = $this->retrieve_variable(Request :: get(PackageManager :: PARAM_VARIABLE));
		$language_pack_id = $variable->get_language_pack_id();

		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_edit)
   		{
   		    Display :: not_allowed();
   		}

		$form = new VariableForm(VariableForm :: TYPE_EDIT, $variable, $this->get_url(array(PackageManager :: PARAM_VARIABLE => $variable->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable();
                        $object = Translation :: get('Variable');
                        $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                              Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
			$this->redirect($message, !$success,
						    array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES, PackageManager :: PARAM_LANGUAGE_PACK => $language_pack_id));
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
    	$variable = PackageDataManager :: get_instance()->retrieve_variable(Request :: get(PackageManager :: PARAM_VARIABLE));
		$language_pack_id = $variable->get_language_pack_id();
		
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('PackageManagerAdminLanguagePacksBrowserComponent')));
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES, PackageManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('PackageManagerAdminVariablesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_variable_updater');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE);
    }
}
?>