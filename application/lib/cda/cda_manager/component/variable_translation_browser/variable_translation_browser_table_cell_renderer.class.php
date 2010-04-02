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
 * @author Hans De Bisschop
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
			case Translation :: get('EnglishTranslation') :
				$translation = $this->browser->retrieve_english_translation($variable_translation->get_variable_id());
				return $translation ? $translation->get_translation() : '';

			case Variable :: PROPERTY_VARIABLE :
				return $this->browser->retrieve_variable($variable_translation->get_variable_id())->get_variable();

			case VariableTranslation :: PROPERTY_RATING :
				return Display :: get_rating_bar($variable_translation->get_relative_rating() * 10, false);
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

		$status = $variable_translation->get_status();
		$can_translate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $variable_translation->get_language_id(), 'cda_language');
		$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $variable_translation->get_language_id(), 'cda_language');

		$theme_image_path = Theme :: get_image_path();
		$theme_common_image_path = Theme :: get_common_image_path();

		if (($can_translate && !$variable_translation->is_locked()) || $can_lock)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_update_variable_translation_url($variable_translation),
				'label' => Translation :: get('Translate'),
				'img' => $theme_image_path.'action_translate.png'
			);

			if ($variable_translation->is_outdated())
			{
				$toolbar_data[] = array(
					'href' => $this->browser->get_verify_variable_translation_url($variable_translation),
					'label' => Translation :: get('Verify'),
					'img' => $theme_image_path.'action_verify.png',
					'confirm' => true
				);

				$toolbar_data[] = array(
					'label' => Translation :: get('DeprecationNotPossible'),
					'img' => $theme_image_path.'action_deprecate_na.png'
				);
			}
			else
			{
				$toolbar_data[] = array(
					'label' => Translation :: get('VerificationNotPossible'),
					'img' => $theme_image_path.'action_verify_na.png'
				);

				$toolbar_data[] = array(
					'href' => $this->browser->get_deprecate_variable_translation_url($variable_translation),
					'label' => Translation :: get('Deprecate'),
					'img' => $theme_image_path.'action_deprecate.png',
					'confirm' => true
				);
			}
		}
		elseif ($can_translate && $variable_translation->is_locked())
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Lock'),
				'img' => $theme_common_image_path.'action_lock.png'
			);
		}

		if ($can_lock)
		{
			if (!$variable_translation->is_locked())
			{
				$toolbar_data[] = array(
					'href' => $this->browser->get_lock_variable_translation_url($variable_translation),
					'label' => Translation :: get('Lock'),
					'img' => $theme_common_image_path.'action_lock.png'
				);
			}
			else
			{
				$toolbar_data[] = array(
					'href' => $this->browser->get_unlock_variable_translation_url($variable_translation),
					'label' => Translation :: get('Unlock'),
					'img' => $theme_common_image_path.'action_unlock.png'
				);
			}
		}

		$toolbar_data[] = array(
			'href' => $this->browser->get_rate_variable_translation_url($variable_translation),
			'label' => Translation :: get('Rate'),
			'img' => $theme_common_image_path.'action_statistics.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_view_variable_translation_url($variable_translation),
			'label' => Translation :: get('View'),
			'img' => $theme_common_image_path.'action_browser.png'
		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>