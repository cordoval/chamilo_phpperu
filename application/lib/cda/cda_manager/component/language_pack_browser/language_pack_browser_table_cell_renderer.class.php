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
		$can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $cda_language_id, 'cda_language');
		$can_translate = CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $cda_language_id, 'cda_language');

		$toolbar_data = array();

		if(get_class($this->browser) != 'CdaManagerLanguagePacksBrowserComponent')
		{
			$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, 'language_pack', 'manager');
    		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, 'language_pack', 'manager');

    		if ($can_edit)
    		{
    			$toolbar_data[] = array(
    				'href' => $this->browser->get_update_language_pack_url($language_pack),
    				'label' => Translation :: get('Edit'),
    				'img' => Theme :: get_common_image_path().'action_edit.png'
    			);
    		}

    		if ($can_delete)
    		{
    			$toolbar_data[] = array(
    				'href' => $this->browser->get_delete_language_pack_url($language_pack),
    				'label' => Translation :: get('Delete'),
    				'img' => Theme :: get_common_image_path().'action_delete.png',
    			);
    		}
		}
		else
		{
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
		        else
		        {
		        	$toolbar_data[] = array(
						'label' => Translation :: get('LockNa'),
						'img' => Theme :: get_common_image_path().'action_lock_na.png'
					);
		        }

		        if($this->browser->can_language_pack_be_unlocked($language_pack, $this->browser->get_cda_language()))
		        {
		        	$toolbar_data[] = array(
						'href' => $this->browser->get_unlock_language_pack_url($language_pack, $cda_language_id),
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

				$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $this->browser->get_cda_language());
				$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
				$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID,
													   'cda_' . Variable :: get_table_name(), $subcondition);
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