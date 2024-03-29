<?php

namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\AdministrationComponent;
use common\libraries\Utilities;

/**
 * @package application.cda.cda.component
 */

/**
 * Component to delete cda_languages objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguageDeleterComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
	   	$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');

   		if (!$can_delete)
   		{
   		    Display :: not_allowed();
   		}

		$ids = $_GET[CdaManager :: PARAM_CDA_LANGUAGE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$cda_language = $this->retrieve_cda_language($id);

				if (!$cda_language->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('Language')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECTS' => Translation :: get('Language')), Utilities :: COMMON_LIBRARIES);
                                }
			}
			else
			{
				if (count($ids) == 1)
				{
                                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('Languages')), Utilities :: COMMON_LIBRARIES);
				}
				else
				{
                                    $message = Translation :: get('ObjectsDeleted', array('OBJECTS' => Translation :: get('Languages')), Utilities :: COMMON_LIBRARIES);
				}
			}

			$this->redirect($message, !$failures, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_languages_deleter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES)), Translation :: get('CdaManagerAdminCdaLanguagesBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_CDA_LANGUAGE);
    }
}
?>