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
			case VariableTranslation :: PROPERTY_RATING :
				return $variable_translation->get_relative_rating();
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
		
		if($variable_translation->get_status() == VariableTranslation :: STATUS_NORMAL)
        {
			$toolbar_data[] = array(
				'href' => $this->browser->get_update_variable_translation_url($variable_translation),
				'label' => Translation :: get('Translate'),
				'img' => Theme :: get_image_path().'action_translate.png'
			);
			
        	$toolbar_data[] = array(
				'href' => $this->browser->get_lock_variable_translation_url($variable_translation),
				'label' => Translation :: get('Lock'),
				'img' => Theme :: get_common_image_path().'action_lock.png'
			);
			
			$toolbar_data[] = array(
				'label' => Translation :: get('UnlockNa'),
				'img' => Theme :: get_common_image_path().'action_unlock_na.png'
			);
        }
        else
        {
        	$toolbar_data[] = array(
				'label' => Translation :: get('TranslateNa'),
				'img' => Theme :: get_image_path().'action_translate_na.png'
			);
			
        	$toolbar_data[] = array(
				'label' => Translation :: get('LockNa'),
				'img' => Theme :: get_common_image_path().'action_lock_na.png'
			);
			
        	$toolbar_data[] = array(
				'href' => $this->browser->get_unlock_variable_translation_url($variable_translation),
				'label' => Translation :: get('Unlock'),
				'img' => Theme :: get_common_image_path().'action_unlock.png'
			);
        }
        
		$toolbar_data[] = array(
			'href' => $this->browser->get_rate_variable_translation_url($variable_translation),
			'label' => Translation :: get('Rate'),
			'img' => Theme :: get_common_image_path().'action_statistics.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_view_variable_translation_url($variable_translation),
			'label' => Translation :: get('View'),
			'img' => Theme :: get_common_image_path().'action_browser.png'
		);
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>