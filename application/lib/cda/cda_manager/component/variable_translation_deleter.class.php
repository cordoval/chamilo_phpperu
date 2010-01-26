<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete variable_translations objects
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CdaManager :: PARAM_VARIABLE_TRANSLATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$variable_translation = $this->retrieve_variable_translation($id);

				if (!$variable_translation->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationNotDeleted';
				}
				else
				{
					$message = 'SelectedVariableTranslationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationDeleted';
				}
				else
				{
					$message = 'SelectedVariableTranslationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoVariableTranslationsSelected')));
		}
	}
}
?>