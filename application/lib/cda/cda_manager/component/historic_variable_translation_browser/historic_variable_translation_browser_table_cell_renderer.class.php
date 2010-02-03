<?php
/**
 * @package cda.tables.variable_translation_table
 */
require_once dirname(__FILE__).'/historic_variable_translation_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/historic_variable_translation_table/default_historic_variable_translation_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../historic_variable_translation.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Cell renderer for the historic variable translation browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class HistoricVariableTranslationBrowserTableCellRenderer extends DefaultHistoricVariableTranslationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function HistoricVariableTranslationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $historic_variable_translation)
	{
		if ($column === HistoricVariableTranslationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($historic_variable_translation);
		}

		switch ($column->get_name())
		{
			case Translation :: get('Rating') :
				$percentage = $historic_variable_translation->get_relative_rating() * 10;
				return Display :: get_rating_bar($percentage, false);
		}

		return parent :: render_cell($column, $historic_variable_translation);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($historic_variable_translation)
	{
		$toolbar_data = array();

//		$status = $variable_translation->get_status();
//		$can_translate = CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $variable_translation->get_language_id(), 'cda_language');
		$can_delete = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $historic_variable_translation->get_variable_translation()->get_language_id(), 'cda_language');

		if ($can_delete)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_delete_historic_variable_translation_url($historic_variable_translation),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
			    'confirm' => true
			);

			$toolbar_data[] = array(
				'href' => $this->browser->get_revert_historic_variable_translation_url($historic_variable_translation),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
			    'confirm' => true
			);
		}
//		elseif($can_translate && $variable_translation->is_locked())
//		{
//        	$toolbar_data[] = array(
//				'label' => Translation :: get('Lock'),
//				'img' => Theme :: get_common_image_path().'action_lock.png'
//			);
//		}
//
//		if ($can_lock)
//		{
//			if(!$variable_translation->is_locked())
//	        {
//	        	$toolbar_data[] = array(
//					'href' => $this->browser->get_lock_variable_translation_url($variable_translation),
//					'label' => Translation :: get('Lock'),
//					'img' => Theme :: get_common_image_path().'action_lock.png'
//				);
//	        }
//	        else
//	        {
//	        	$toolbar_data[] = array(
//					'href' => $this->browser->get_unlock_variable_translation_url($variable_translation),
//					'label' => Translation :: get('Unlock'),
//					'img' => Theme :: get_common_image_path().'action_unlock.png'
//				);
//	        }
//		}
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_rate_variable_translation_url($variable_translation),
//			'label' => Translation :: get('Rate'),
//			'img' => Theme :: get_common_image_path().'action_statistics.png'
//		);
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_view_variable_translation_url($variable_translation),
//			'label' => Translation :: get('View'),
//			'img' => Theme :: get_common_image_path().'action_browser.png'
//		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>