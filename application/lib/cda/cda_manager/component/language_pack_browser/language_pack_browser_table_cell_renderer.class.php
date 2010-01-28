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
 * @author 
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
		$toolbar_data = array();

		if(get_class($this->browser) != 'CdaManagerLanguagePacksBrowserComponent')
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_update_language_pack_url($language_pack),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
	
			$toolbar_data[] = array(
				'href' => $this->browser->get_delete_language_pack_url($language_pack),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path().'action_delete.png',
			);
		}
		else
		{
			$cda_language_id = $this->browser->get_cda_language();
			$can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $cda_language_id, 'cda_language');
			
			if ($can_lock)
			{
				if($this->browser->can_language_pack_be_locked($language_pack, $this->browser->get_cda_language()))
		        {
		        	$toolbar_data[] = array(
						'href' => $this->browser->get_lock_language_pack_url($language_pack, $cda_language_id),
						'label' => Translation :: get('Lock'),
						'img' => Theme :: get_common_image_path().'action_lock.png'
					);
		        }
//		        else
//		        {
//		        	$toolbar_data[] = array(
//						'label' => Translation :: get('LockNa'),
//						'img' => Theme :: get_common_image_path().'action_lock_na.png'
//					);
//		        }
		        
		        if($this->browser->can_language_pack_be_unlocked($language_pack, $this->browser->get_cda_language()))
		        {
		        	$toolbar_data[] = array(
						'href' => $this->browser->get_unlock_language_pack_url($language_pack, $cda_language_id),
						'label' => Translation :: get('Unlock'),
						'img' => Theme :: get_common_image_path().'action_unlock.png'
					);
		        }
//		        else
//		        {
//					$toolbar_data[] = array(
//						'label' => Translation :: get('UnlockNa'),
//						'img' => Theme :: get_common_image_path().'action_unlock_na.png'
//					);
//		        }
			}
		}
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>