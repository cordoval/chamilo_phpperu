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
 * Component to delete variables objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariableDeleterComponent extends PackageManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_delete)
   		{
   		    Display :: not_allowed();
   		}

		$ids = $_GET[PackageManager :: PARAM_VARIABLE];
		$failures = 0;
		$language_pack_id = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$variable = $this->retrieve_variable($id);

				if(!$language_pack_id)
					$language_pack_id = $variable->get_language_pack_id();

				if (!$variable->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('Variable')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECTS' => Translation :: get('Variables')), Utilities :: COMMON_LIBRARIES);
                                }
			}
			else
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('Variable')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsDeleted', array('OBJECTS' => Translation :: get('Variables')), Utilities :: COMMON_LIBRARIES);
				}
			}

			$this->redirect($message, $failures,
						    array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES, PackageManager :: PARAM_LANGUAGE_PACK => $language_pack_id));
		}
		else
		{
			$this->display_error_page(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('PackageManagerAdminLanguagePacksBrowserComponent')));
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_BROWSE_VARIABLES, PackageManager :: PARAM_LANGUAGE_PACK => Request :: get(PackageManager :: PARAM_LANGUAGE_PACK))), Translation :: get('PackageManagerAdminVariablesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_variable_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_VARIABLE);
    }
}
?>