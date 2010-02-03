<?php
/**
 * cda
 */

/**
 * This class describes a HistoricVariableTranslation data object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class HistoricVariableTranslation extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * HistoricVariableTranslation properties
	 */
	const PROPERTY_VARIABLE_TRANSLATION_ID = 'variable_translation_id';
	const PROPERTY_TRANSLATION = 'translation';
	const PROPERTY_DATE = 'date';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_RATING = 'rating';
	const PROPERTY_RATED = 'rated';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_VARIABLE_TRANSLATION_ID, self :: PROPERTY_TRANSLATION, self :: PROPERTY_DATE, self :: PROPERTY_USER_ID, self :: PROPERTY_RATING, self :: PROPERTY_RATED));
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the variable_translation_id of this HistoricVariableTranslation.
	 * @return the variable_translation_id.
	 */
	function get_variable_translation_id()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE_TRANSLATION_ID);
	}

	function get_variable_translation()
	{
	    return $this->get_data_manager()->retrieve_variable_translation($this->get_variable_translation_id());
	}

	/**
	 * Sets the variable_translation_id of this HistoricVariableTranslation.
	 * @param variable_translation_id
	 */
	function set_variable_translation_id($variable_translation_id)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE_TRANSLATION_ID, $variable_translation_id);
	}

	/**
	 * Returns the translation of this HistoricVariableTranslation.
	 * @return the translation.
	 */
	function get_translation()
	{
		return $this->get_default_property(self :: PROPERTY_TRANSLATION);
	}

	/**
	 * Sets the translation of this HistoricVariableTranslation.
	 * @param translation
	 */
	function set_translation($translation)
	{
		$this->set_default_property(self :: PROPERTY_TRANSLATION, $translation);
	}

	/**
	 * Returns the date of this HistoricVariableTranslation.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this HistoricVariableTranslation.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}

	/**
	 * Returns the user_id of this HistoricVariableTranslation.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	function get_user()
	{
		return UserDataManager :: get_instance()->retrieve_user($this->get_user_id());
	}

	/**
	 * Sets the user_id of this HistoricVariableTranslation.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

	/**
	 * Returns the rating of this HistoricVariableTranslation.
	 * @return the rating.
	 */
	function get_rating()
	{
		return $this->get_default_property(self :: PROPERTY_RATING);
	}

	/**
	 * Sets the rating of this HistoricVariableTranslation.
	 * @param rating
	 */
	function set_rating($rating)
	{
		$this->set_default_property(self :: PROPERTY_RATING, $rating);
	}

	/**
	 * Returns the rated of this HistoricVariableTranslation.
	 * @return the rated.
	 */
	function get_rated()
	{
		return $this->get_default_property(self :: PROPERTY_RATED);
	}

	/**
	 * Sets the rated of this HistoricVariableTranslation.
	 * @param rated
	 */
	function set_rated($rated)
	{
		$this->set_default_property(self :: PROPERTY_RATED, $rated);
	}

	function get_relative_rating()
	{
		return (int)($this->get_rating() / $this->get_rated());
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	function revert()
	{
	    $variable_translation = $this->get_variable_translation();
	    $variable_translation->set_translation($this->get_translation());
	    $variable_translation->set_date(Utilities :: to_db_date(time()));
	    $variable_translation->set_user_id($this->get_user_id());
	    $variable_translation->set_rating($this->get_rating());
	    $variable_translation->set_rated($this->get_rated());

	    return $variable_translation->update();
	}
}

?>