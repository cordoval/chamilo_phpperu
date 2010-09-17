<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';

/**
 * Component to revert historic variable translations objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerHistoricVariableTranslationReverterComponent extends CdaManager
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
				$can_delete = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $historic_variable_translation->get_variable_translation()->get_language_id(), 'cda_language');

				if (!$can_delete)
				{
					$failures++;
				}
				elseif (!$historic_variable_translation->revert())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationNotReverted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotReverted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHistoricVariableTranslationReverted';
				}
				else
				{
					$message = 'SelectedHistoricVariableTranslationsNotReverted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_VIEW_VARIABLE_TRANSLATION, CdaManager :: PARAM_VARIABLE_TRANSLATION => $historic_variable_translation->get_variable_translation_id()));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoHistoricVariableTranslationsSelected')));
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS, CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(self :: PARAM_CDA_LANGUAGE))), Translation :: get('CdaManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS, CdaManager :: PARAM_LANGUAGE_PACK => Request :: get(self :: PARAM_LANGUAGE_PACK), CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(self :: PARAM_CDA_LANGUAGE))), Translation :: get('CdaManagerVariableTranslationsBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_VIEW_VARIABLE_TRANSLATION, CdaManager :: PARAM_VARIABLE_TRANSLATION => Request :: get(self :: PARAM_VARIABLE_TRANSLATION))), Translation :: get('CdaManagerVariableTranslationViewerComponent')));
    	$breadcrumbtrail->add_help('cda_variable_translations_historic_reverter');
    }
    
    function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_HISTORIC_VARIABLE_TRANSLATION, self :: PARAM_VARIABLE_TRANSLATION, self :: PARAM_CDA_LANGUAGE, self :: PARAM_VARIABLE_TRANSLATION);
    }
}
?>