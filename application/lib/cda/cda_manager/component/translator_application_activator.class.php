<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete language_packs objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerTranslatorApplicationActivatorComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CdaManager :: PARAM_TRANSLATOR_APPLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$translator_application = $this->retrieve_translator_application($id);
				$can_activate = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $translator_application->get_destination_language_id(), 'cda_language');
				
				if (!$can_activate)
				{
					$failures++;
				}
				elseif (!$translator_application->activate())
				{
					$failures++;
				}
				else
				{
					$this->notify($translator_application);
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationNotActivated';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotActivated';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedTranslatorApplicationActivated';
				}
				else
				{
					$message = 'SelectedTranslatorApplicationsNotActivated';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_TRANSLATOR_APPLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoTranslatorApplicationsSelected')));
		}
	}
	
	function notify($translator_application)
    {
    	$user = UserDataManager :: get_instance()->retrieve_user($translator_application->get_user_id());
    	$source_language = $this->retrieve_cda_language($translator_application->get_source_language_id());
    	$destination_language = $this->retrieve_cda_language($translator_application->get_destination_language_id());
    	
    	$html[] = Translation :: get('Dear') . ' ' . $user->get_fullname();
    	$html[] = '';
    	$html[] = Translation :: get('YouHaveBeenAcceptedAsTranslatorFor');
    	$html[] = '';
    	$html[] = Translation :: get('SourceLanguage');
    	$html[] = $source_language->get_original_name();
    	$html[] = '';
    	$html[] = Translation :: get('DestinationLanguage');
    	$html[] = $destination_language->get_original_name();

    	$subject = '[CDA] ' . Translation :: get('TranslationApplicationAccepted');
    	$content = implode("<br />", $html);
    	$from = PlatformSetting :: get('administrator_email');
    	$to = $user->get_email();
    	$mail = Mail :: factory($subject, $content, $to, $from); 
    	$mail->send();
    }
}
?>