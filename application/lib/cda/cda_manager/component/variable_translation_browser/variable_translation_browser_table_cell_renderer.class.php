<?php
/**
 * @package cda.tables.variable_translation_table
 */
require_once dirname(__FILE__).'/variable_translation_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/variable_translation_table/default_variable_translation_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../variable_translation.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class VariableTranslationBrowserTableCellRenderer extends DefaultVariableTranslationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function VariableTranslationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $variable_translation)
	{
		if ($column === VariableTranslationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($variable_translation);
		}
		
		switch ($column->get_name())
		{
			case 'EnglishTranslation' :
				
			$translation = $this->browser->retrieve_english_translation($variable_translation->get_variable_id());
			if($translation)
			{
				return $translation->get_translation();
			}else
			{
				return '';
			}
				
			case VariableTranslation :: PROPERTY_VARIABLE_ID :
				$variable_id = $variable_translation->get_variable_id();
				$variable = $this->browser->retrieve_variable($variable_id);
				return $variable->get_variable();
		}
		
		return parent :: render_cell($column, $variable_translation);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($variable_translation)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_variable_translation_url($variable_translation),
			'label' => Translation :: get('Translate'),
			'img' => Theme :: get_common_image_path().'action_translate.png'
		);
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>