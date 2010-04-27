<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';

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
					$message = 'SelectedLanguagePackNotDeleted';
				}
				else
				{
					$message = 'SelectedLanguagePacksNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLanguagePackDeleted';
				}
				else
				{
					$message = 'SelectedLanguagePacksNotDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLanguagePacksSelected')));
		}
	}
}
?>