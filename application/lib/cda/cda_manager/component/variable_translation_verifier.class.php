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
class CdaManagerVariableTranslationVerifierComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION);
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

        		$language_id = $variable_translation->get_language_id();
        		$variable_id = $variable_translation->get_variable_id();
        		$variable = $this->retrieve_variable($variable_id);

        		$can_translate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $language_id, 'cda_language');
        		$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $language_id, 'cda_language');

				if (!($can_translate && !$variable_translation->is_locked()) && !$can_lock)
				{
					$failures++;
				}
				elseif (!$variable_translation->verify())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationNotVerified';
				}
				else
				{
					$message = 'SelectedVariableTranslationsNotVerified';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableTranslationVerified';
				}
				else
				{
					$message = 'SelectedVariableTranslationsNotVerified';
				}
			}

			$parameters = array();
			$parameters[CdaManager :: PARAM_ACTION] = CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS;
			$parameters[CdaManager :: PARAM_CDA_LANGUAGE] = $language_id;
			$parameters[CdaManager :: PARAM_LANGUAGE_PACK] = $variable->get_language_pack_id();

			$this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoVariableTranslationsSelected')));
		}
	}
}
?>