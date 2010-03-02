<?php
/**
 * cda
 */

/**
 * This class describes a CdaLanguage data object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaLanguage extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * CdaLanguage properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ORIGINAL_NAME = 'original_name';
	const PROPERTY_ENGLISH_NAME = 'english_name';
	const PROPERTY_ISOCODE = 'isocode';
	const PROPERTY_RTL = 'rtl';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_NAME, self :: PROPERTY_ENGLISH_NAME, self :: PROPERTY_ISOCODE, self :: PROPERTY_RTL);
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the id of this CdaLanguage.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this CdaLanguage.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the original_name of this CdaLanguage.
	 * @return the original_name.
	 */
	function get_original_name()
	{
		return $this->get_default_property(self :: PROPERTY_ORIGINAL_NAME);
	}

	/**
	 * Sets the original_name of this CdaLanguage.
	 * @param original_name
	 */
	function set_original_name($original_name)
	{
		$this->set_default_property(self :: PROPERTY_ORIGINAL_NAME, $original_name);
	}

	/**
	 * Returns the english_name of this CdaLanguage.
	 * @return the english_name.
	 */
	function get_english_name()
	{
		return $this->get_default_property(self :: PROPERTY_ENGLISH_NAME);
	}

	/**
	 * Sets the english_name of this CdaLanguage.
	 * @param english_name
	 */
	function set_english_name($english_name)
	{
		$this->set_default_property(self :: PROPERTY_ENGLISH_NAME, $english_name);
	}

	/**
	 * Returns the isocode of this CdaLanguage.
	 * @return the isocode.
	 */
	function get_isocode()
	{
		return $this->get_default_property(self :: PROPERTY_ISOCODE);
	}

	/**
	 * Sets the isocode of this CdaLanguage.
	 * @param isocode
	 */
	function set_isocode($isocode)
	{
		$this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
	}

	/**
	 * Returns the rtl of this CdaLanguage.
	 * @return the rtl.
	 */
	function get_rtl()
	{
		return $this->get_default_property(self :: PROPERTY_RTL);
	}

	/**
	 * Sets the rtl of this CdaLanguage.
	 * @param rtl
	 */
	function set_rtl($rtl)
	{
		$this->set_default_property(self :: PROPERTY_RTL, $rtl);
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	function create()
	{
		$dm = $this->get_data_manager();

		$languages = $dm->retrieve_cda_languages();
		while($language = $languages->next_result())
			if($language->get_english_name() == $this->get_english_name())
				return false;

		$succes = parent :: create();

		$variables = $dm->retrieve_variables();

		while($variable = $variables->next_result())
		{
			$translation = new VariableTranslation();
			$translation->set_user_id(0);
			$translation->set_language_id($this->get_id());
			$translation->set_variable_id($variable->get_id());
			$translation->set_date(Utilities :: to_db_date(time()));
			$translation->set_rated(0);
			$translation->set_rating(0);
			$translation->set_translation(' ');
			$translation->set_status(VariableTranslation :: STATUS_NORMAL);
			if (! $translation->create())
			{
				return false;
			}
		}

	    $parent = CdaRights :: get_languages_subtree_root_id();
		
		if(!CdaRights :: create_location_in_languages_subtree($this->get_english_name(), 'cda_language', $this->get_id(), $parent))
		{
			return false;
		}

		return $succes;
	}

	function update()
	{
		$dm = $this->get_data_manager();

		$condition = new NotCondition(new EqualityCondition(CdaLanguage :: PROPERTY_ID, $this->get_id()));
    	$languages = $dm->retrieve_cda_languages($condition);
		while($language = $languages->next_result())
			if($language->get_english_name() == $this->get_english_name())
				return false;

		return parent :: update();
	}

	function delete()
	{
		$succes = parent :: delete();
		$dm = $this->get_data_manager();

		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $this->get_id());
		$translations = $dm->retrieve_variable_translations($condition);

		while($translation = $translations->next_result())
		{
			$succes &= $translation->delete();
		}

		return $succes;

	}

	function is_outdated()
	{
	    $count = $this->get_data_manager()->get_status_for_language($this);
	    return $count > 0;
	}

	function get_status_icon($language_id = null)
	{
		if ($this->is_outdated($language_id))
	    {
	        return '<img src="' . Theme :: get_image_path() . 'status_outdated.png" title="' . Translation :: get('OneOrMoreTranslationsOutdated') . '" alt="' . Translation :: get('OneOrMoreTranslationsOutdated') . '" />';
	    }
	    else
	    {
	        return '<img src="' . Theme :: get_image_path() . 'status_normal.png" title="' . Translation :: get('TranslationFinishedOrInProgress') . '" alt="' . Translation :: get('TranslationFinishedOrInProgress') . '" />';
	    }
	}
}

?>