<?php
/**
 * @package cda.tables.cda_language_table
 */
require_once dirname(__FILE__).'/cda_language_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/cda_language_table/default_cda_language_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../cda_language.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class CdaLanguageBrowserTableCellRenderer extends DefaultCdaLanguageTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function CdaLanguageBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $cda_language)
	{
		if ($column === CdaLanguageBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($cda_language);
		}

		switch ($column->get_name())
		{
			case CdaLanguage :: PROPERTY_ORIGINAL_NAME :
				
				/*if(get_class($this->browser) == 'CdaManagerCdaLanguagesBrowserComponent')
				{
					$url = $this->browser->get_browse_language_packs_url($cda_language->get_id());
					return '<a href="' . $url . '">' . $cda_language->get_original_name() . '</a>';
				}*/
				
				if(!$this->browser->get_user()->is_platform_admin() && 
					CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $cda_language->get_id(), 'cda_language'))
				{
					return '<span style="color: green; font-weight: bold;">' . $cda_language->get_original_name() . '</span>'; 
				}
				
				return $cda_language->get_original_name();
			case CdaLanguage :: PROPERTY_ENGLISH_NAME :
				if(get_class($this->browser) == 'CdaManagerCdaLanguagesBrowserComponent')
				{
					$url = $this->browser->get_browse_language_packs_url($cda_language->get_id());
					return '<a href="' . $url . '">' . $cda_language->get_english_name() . '</a>';
				}
				
				return $cda_language->get_english_name();
			case 'TranslationProgress':
				$percentage = $this->browser->get_progress_for_language($cda_language);
				return Display :: get_progress_bar($percentage);
		}
		
		return parent :: render_cell($column, $cda_language);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($cda_language)
	{
		$toolbar_data = array();

		if(get_class($this->browser) != 'CdaManagerCdaLanguagesBrowserComponent')
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_update_cda_language_url($cda_language),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);

			$toolbar_data[] = array(
				'href' => $this->browser->get_delete_cda_language_url($cda_language),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path().'action_delete.png',
			);
		}
		else
		{
			$can_translate = CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $cda_language->get_id(), 'cda_language');
			$can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $cda_language->get_id(), 'cda_language');
			
			if ($can_lock)
			{
				if($this->browser->can_language_be_locked($cda_language))
		        {
		        	$toolbar_data[] = array(
						'href' => $this->browser->get_lock_language_url($cda_language),
						'label' => Translation :: get('Lock'),
						'img' => Theme :: get_common_image_path().'action_lock.png'
					);
		        }
		        else
		        {
		        	$toolbar_data[] = array(
						'label' => Translation :: get('LockNa'),
						'img' => Theme :: get_common_image_path().'action_lock_na.png'
					);
		        }
		        
		        if($this->browser->can_language_be_unlocked($cda_language))
		        {
		        	$toolbar_data[] = array(
						'href' => $this->browser->get_unlock_language_url($cda_language),
						'label' => Translation :: get('Unlock'),
						'img' => Theme :: get_common_image_path().'action_unlock.png'
					);
		        }
		        else
		        {
					$toolbar_data[] = array(
						'label' => Translation :: get('UnlockNa'),
						'img' => Theme :: get_common_image_path().'action_unlock_na.png'
					);
		        }
			}
			
			if ($can_translate || $can_lock)
			{
				if (!$can_lock)
				{
					$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
				}
				$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $cda_language->get_id());
				$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATION, ' ');
				$condition = new AndCondition($conditions);
				$translation = $this->browser->retrieve_variable_translations($condition, 0, 1)->next_result();
				
				if($translation)
				{
					$toolbar_data[] = array(
								'href' => $this->browser->get_update_variable_translation_url($translation),
								'label' => Translation :: get('TranslateFirstEmptyTranslation'),
								'img' => Theme :: get_image_path() . 'action_quickstart.png'
							);
				}
			}
		}
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>