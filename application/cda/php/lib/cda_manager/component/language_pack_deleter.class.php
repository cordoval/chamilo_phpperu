<?php

namespace application\cda;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Utilities;
/**
 * @package application.cda.cda.component
 */

/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerLanguagePackDeleterComponent extends CdaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');

   		if (!$can_delete)
   		{
   		    Display :: not_allowed();
   		}

		$ids = $_GET[CdaManager :: PARAM_LANGUAGE_PACK];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$language_pack = $this->retrieve_language_pack($id);

				if (!$language_pack->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('LanguagePack')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECTS' => Translation :: get('LanguagePacks')), Utilities :: COMMON_LIBRARIES);
                                				}
			}
			else
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('LanguagePack')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsDeleted', array('OBJECTS' => Translation :: get('LanguagePacks')), Utilities :: COMMON_LIBRARIES);
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_language_pack_deleter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('CdaManagerAdminLanguagePacksBrowserComponent')));
    }
	
 	function get_additional_parameters()
    {
    	return array(self :: PARAM_LANGUAGE_PACK);
    }
}
?>