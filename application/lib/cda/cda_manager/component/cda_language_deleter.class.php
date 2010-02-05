<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete cda_languages objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerCdaLanguageDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
	   	$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, 'cda_language', 'manager');

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
					$message = 'SelectedCdaLanguageNotDeleted';
				}
				else
				{
					$message = 'SelectedCdaLanguagesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCdaLanguageDeleted';
				}
				else
				{
					$message = 'SelectedCdaLanguagesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_CDA_LANGUAGES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCdaLanguagesSelected')));
		}
	}
}
?>