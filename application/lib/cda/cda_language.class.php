<?php 
/**
 * cda
 */

/**
 * This class describes a CdaLanguage data object
 * @author Sven Vanpoucke
 * @author 
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

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_NAME, self :: PROPERTY_ENGLISH_NAME, self :: PROPERTY_ISOCODE);
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


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function create()
	{
		$succes = parent :: create();
		
		$dm = $this->get_data_manager();
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
			$succes &= $translation->create();
		}

		return $succes;
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
}

?>