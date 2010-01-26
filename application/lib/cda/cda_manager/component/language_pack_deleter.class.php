<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerLanguagePackDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
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
					$message = 'SelectedLanguagePackDeleted';
				}
				else
				{
					$message = 'SelectedLanguagePackDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLanguagePacksDeleted';
				}
				else
				{
					$message = 'SelectedLanguagePacksDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLanguagePacksSelected')));
		}
	}
}
?>