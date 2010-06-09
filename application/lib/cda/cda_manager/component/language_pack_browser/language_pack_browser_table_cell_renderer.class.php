<?php
/**
 * @package cda.tables.language_pack_table
 */
require_once dirname(__FILE__).'/language_pack_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/language_pack_table/default_language_pack_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../language_pack.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class LanguagePackBrowserTableCellRenderer extends DefaultLanguagePackTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function LanguagePackBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $language_pack)
	{
		if ($column === LanguagePackBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($language_pack);
		}

		switch ($column->get_name())
		{
			case LanguagePack :: PROPERTY_NAME :

				if(get_class($this->browser) == 'CdaManagerLanguagePacksBrowserComponent')
				{
					$url = $this->browser->get_browse_variable_translations_url(Request :: get(CdaManager :: PARAM_CDA_LANGUAGE), $language_pack->get_id());
				}
				else
				{
					$url = $this->browser->get_admin_browse_variables_url($language_pack->get_id());
				}

				return '<a href="' . $url . '">' . $language_pack->get_name() . '</a>';
			case LanguagePack :: PROPERTY_TYPE :
				return $language_pack->get_type_name();
			case Translation :: get('Status'):
			    return $language_pack->get_status_icon();
			case Translation :: get('TranslationProgress'):
				$percentage = $this->browser->get_progress_for_language_pack($language_pack, $this->browser->get_cda_language());
				return Display :: get_progress_bar($percentage);
		}

		return parent :: render_cell($column, $language_pack);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($language_pack)
	{
		$cda_language_id = $this->browser->get_cda_language();
		$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $cda_language_id, 'cda_language');
		$can_translate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $cda_language_id, 'cda_language');

		$toolbar = new Toolbar();

		if(get_class($this->browser) != 'CdaManagerLanguagePacksBrowserComponent')
		{
			$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');
    		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');

    		if ($can_edit)
    		{
    			$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_language_pack_url($language_pack), ToolbarItem :: DISPLAY_ICON));
    		}

    		if ($can_delete)
    		{
    			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Delete'), 
    				Theme :: get_common_image_path() . 'action_delete.png', 
    				$this->browser->get_delete_language_pack_url($language_pack), 
    				ToolbarItem :: DISPLAY_ICON,
    				true
    				));
    		}
		}
		else
		{
			if ($can_lock)
			{
				if($this->browser->can_language_pack_be_locked($language_pack, $cda_language_id))
		        {
		        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Lock'), 
    				Theme :: get_common_image_path() . 'action_lock.png', 
    				$this->browser->get_lock_language_pack_url($language_pack, $cda_language_id), 
    				ToolbarItem :: DISPLAY_ICON,
    				true
    				));
		        }
		        else
		        {
		        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('LockNa'), 
    				Theme :: get_common_image_path() . 'action_lock_na.png', 
    				null, 
    				ToolbarItem :: DISPLAY_ICON
    				));
		        }

		        if($this->browser->can_language_pack_be_unlocked($language_pack, $cda_language_id))
		        {
		        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Unlock'), 
    				Theme :: get_common_image_path() . 'action_unlock.png', 
    				$this->browser->get_unlock_language_pack_url($language_pack, $cda_language_id), 
    				ToolbarItem :: DISPLAY_ICON
    				));
		        }
		        else
		        {
		        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('UnlockNa'), 
    				Theme :: get_common_image_path() . 'action_unlock_na.png', 
    				null, 
    				ToolbarItem :: DISPLAY_ICON
    				));
		        }
			}

			if ($can_translate || $can_lock)
			{
				if (!$can_lock)
				{
					$status = VariableTranslation :: STATUS_NORMAL;
				}

				$translation = $this->browser->retrieve_first_untranslated_variable_translation($cda_language_id, $language_pack->get_id(), $status);
				
				if($translation)
				{
					$toolbar->add_item(new ToolbarItem(
    				Translation :: get('TranslateFirstEmptyTranslation'), 
    				Theme :: get_common_image_path() . 'action_quickstart.png', 
    				$this->browser->get_update_variable_translation_url($translation), 
    				ToolbarItem :: DISPLAY_ICON
    				));
				}
			}
		}

		return $toolbar->as_html();
	}
}
?>