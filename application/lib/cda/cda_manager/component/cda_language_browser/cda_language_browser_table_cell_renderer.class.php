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
 * @author Hans De Bisschop
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
					CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $cda_language->get_id(), 'cda_language'))
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
			case Translation :: get('TranslationProgress') :
				$percentage = $this->browser->get_progress_for_language($cda_language);
				return Display :: get_progress_bar($percentage);
			case CdaLanguage :: PROPERTY_RTL :
				if($cda_language->get_rtl())
				{
					return '<img src="' . Theme :: get_image_path() . 'orientation_right.png" title="' . Translation :: get('RightToLeft') . '" alt="' . Translation :: get('RightToLeft') . '" />';
				}
				else
				{
				    return '<img src="' . Theme :: get_image_path() . 'orientation_left.png" title="' . Translation :: get('LeftToRight') . '" alt="' . Translation :: get('LeftToRight') . '" />';
				}
				return Translation :: get('False');
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
		$toolbar = new Toolbar();

		if(get_class($this->browser) != 'CdaManagerCdaLanguagesBrowserComponent')
		{
    		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');
    		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGES, 'manager');

    		if ($can_edit)
    		{
    			$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_cda_language_url($cda_language), ToolbarItem :: DISPLAY_ICON));
    		}

    		if ($can_delete)
    		{
    			$toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_cda_language_url($cda_language), ToolbarItem :: DISPLAY_ICON, true));
    		}
		}
		else
		{
			$can_translate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $cda_language->get_id(), 'cda_language');
			$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $cda_language->get_id(), 'cda_language');

			if ($can_lock)
			{
				if($this->browser->can_language_be_locked($cda_language))
		        {
		        	$toolbar->add_item(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', $this->browser->get_lock_language_url($cda_language), ToolbarItem :: DISPLAY_ICON));
		        }
		        else
		        {
		        	$toolbar->add_item(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png', null, ToolbarItem :: DISPLAY_ICON));		        	
		        }

		        if($this->browser->can_language_be_unlocked($cda_language))
		        {
		        	$toolbar->add_item(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path().'action_unlock.png', $this->browser->get_unlock_language_url($cda_language), ToolbarItem :: DISPLAY_ICON));
		        }
		        else
		        {
		        	$toolbar->add_item(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png', null, ToolbarItem :: DISPLAY_ICON));
		        }
			}

			if ($can_translate || $can_lock)
			{
				if (!$can_lock)
				{
					$status = VariableTranslation :: STATUS_NORMAL;
				}
				
				$translation = $this->browser->retrieve_first_untranslated_variable_translation($cda_language->get_id(), null, $status);

				if($translation)
				{
					$toolbar->add_item(new ToolbarItem(Translation :: get('TranslateFirstEmptyTranslation'), Theme :: get_image_path() . 'action_quickstart.png', $this->browser->get_update_variable_translation_url($translation), ToolbarItem :: DISPLAY_ICON));
				}
			}
		}

		return $toolbar->as_html();
	}
}
?>