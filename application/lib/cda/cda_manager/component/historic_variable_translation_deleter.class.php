<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete historic variable translations objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerHistoricVariableTranslationDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CdaManager :: PARAM_HISTORIC_VARIABLE_TRANSLATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$historic_variable_translation = $this->retrieve_historic_variable_translation($id);
				$can_delete = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $historic_variable_translation->get_variable_translation()->get_language_id(), 'cda_language');

				if (!$can_delete)
				{
					$failures++;
				}
				elseif (!$historic_variable_translation->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationNotDeleted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationDeleted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_VIEW_VARIABLE_TRANSLATION, CdaManager :: PARAM_VARIABLE_TRANSLATION => $historic_variable_translation->get_variable_translation_id()));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoHistoricVariableTranslationsSelected')));
		}
	}
}
?>