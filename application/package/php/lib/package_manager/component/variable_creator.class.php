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
 * Component to create a new variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableCreatorComponent extends PackageManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$language_pack_id = Request :: get(PackageManager :: PARAM_LANGUAGE_PACK);

	   	$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_add)
   		{
   		    Display :: not_allowed();
   		}


		$variable = new Variable();
		$variable->set_language_pack_id($language_pack_id);
		$form = new VariableForm(VariableForm :: TYPE_CREATE, $variable, $this->get_url(array(PackageManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_variable();
                        $object = Translation :: get('Variable');
                        $message = $success ? Translation :: get('ObjectCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                              Translation :: get('ObjectNotCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

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
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('PackageManagerAdminLanguagePacksBrowserComponent')));
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(PackageManager :: PARAM_LANGUAGE_PACK))), Translation :: get('PackageManagerAdminVariablesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_variable_creator');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_LANGUAGE_PACK);
    }
}
?>