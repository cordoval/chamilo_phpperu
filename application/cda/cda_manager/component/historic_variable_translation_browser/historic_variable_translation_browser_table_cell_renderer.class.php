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
		$toolbar = new Toolbar();

		$can_delete = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $historic_variable_translation->get_variable_translation()->get_language_id(), 'cda_language');

		if ($can_delete)
		{
			$toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_historic_variable_translation_url($historic_variable_translation), ToolbarItem :: DISPLAY_ICON, true));

			$toolbar->add_item(new ToolbarItem(Translation :: get('Revert'), Theme :: get_common_image_path() . 'action_revert.png', $this->browser->get_revert_historic_variable_translation_url($historic_variable_translation), ToolbarItem :: DISPLAY_ICON, true));
		}

		return $toolbar->as_html();
	}
}
?>