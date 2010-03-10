<?php
/**
 * @package cda.tables.translator_application_table
 */
require_once dirname(__FILE__).'/translator_application_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/translator_application_table/default_translator_application_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../translator_application.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class TranslatorApplicationBrowserTableCellRenderer extends DefaultTranslatorApplicationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function TranslatorApplicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $translator_application)
	{
		if ($column === TranslatorApplicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($translator_application);
		}

		switch($column->get_name())
		{
			case User :: PROPERTY_FIRSTNAME:
				$user = UserDataManager :: get_instance()->retrieve_user($translator_application->get_user_id());
				if($user)
					return $user->get_fullname();
				return Translation :: get('UserUnknown');
			case User :: PROPERTY_USERNAME:
				$user = UserDataManager :: get_instance()->retrieve_user($translator_application->get_user_id());
				if($user)
					return $user->get_username();
				return Translation :: get('UserUnknown');
			case CdaLanguage :: PROPERTY_ENGLISH_NAME:
				$alias = CdaDataManager :: get_instance()->get_alias(CdaLanguage :: get_table_name());
				if($column->get_storage_unit() == $alias)
				{
					$cda_language = CdaDataManager :: get_instance()->retrieve_cda_language($translator_application->get_source_language_id());
				}
				else
				{
					$cda_language = CdaDataManager :: get_instance()->retrieve_cda_language($translator_application->get_destination_language_id());
				}
				
				return $cda_language->get_english_name();
		}
		
		return parent :: render_cell($column, $translator_application);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($translator_application)
	{
		$toolbar_data = array();
		
		$status = $translator_application->get_status();
		
		if ($status == TranslatorApplication :: STATUS_ACCEPTED)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_deactivate_translator_application_url($translator_application),
				'label' => Translation :: get('Deactivate'),
				'img' => Theme :: get_common_image_path().'action_deactivate.png'
			);
		}
		elseif ($status == TranslatorApplication :: STATUS_PENDING)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_activate_translator_application_url($translator_application),
				'label' => Translation :: get('Activate'),
				'img' => Theme :: get_common_image_path().'action_activate.png'
			);
		}

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_translator_application_url($translator_application),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>