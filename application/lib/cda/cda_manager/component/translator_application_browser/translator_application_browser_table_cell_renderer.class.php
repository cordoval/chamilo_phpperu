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
 * @author 
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
				'img' => Theme :: get_image_path().'action_deactivate.png'
			);
		}
		elseif ($status == TranslatorApplication :: STATUS_PENDING)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_activate_translator_application_url($translator_application),
				'label' => Translation :: get('Activate'),
				'img' => Theme :: get_image_path().'action_activate.png'
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