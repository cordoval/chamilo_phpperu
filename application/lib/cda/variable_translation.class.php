<?php
/**
 * cda
 */
require_once dirname(__FILE__) . '/historic_variable_translation.class.php';

/**
 * This class describes a VariableTranslation data object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class VariableTranslation extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * VariableTranslation properties
	 */
	const PROPERTY_LANGUAGE_ID = 'language_id';
	const PROPERTY_VARIABLE_ID = 'variable_id';
	const PROPERTY_TRANSLATION = 'translation';
	const PROPERTY_DATE = 'date';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_RATING = 'rating';
	const PROPERTY_RATED = 'rated';
	const PROPERTY_STATUS = 'status';

	const STATUS_NORMAL = 1;
	const STATUS_BLOCKED = 2;
	const STATUS_OUTDATED = 3;

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_LANGUAGE_ID, self :: PROPERTY_VARIABLE_ID, self :: PROPERTY_TRANSLATION, self :: PROPERTY_DATE, self :: PROPERTY_USER_ID, self :: PROPERTY_RATING, self :: PROPERTY_RATED, self :: PROPERTY_STATUS));
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the language_id of this VariableTranslation.
	 * @return the language_id.
	 */
	function get_language_id()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE_ID);
	}

	/**
	 * Sets the language_id of this VariableTranslation.
	 * @param language_id
	 */
	function set_language_id($language_id)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE_ID, $language_id);
	}

	/**
	 * Returns the variable_id of this VariableTranslation.
	 * @return the variable_id.
	 */
	function get_variable_id()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE_ID);
	}

	/**
	 * Sets the variable_id of this VariableTranslation.
	 * @param variable_id
	 */
	function set_variable_id($variable_id)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE_ID, $variable_id);
	}

	/**
	 * Returns the translation of this VariableTranslation.
	 * @return the translation.
	 */
	function get_translation()
	{
		return $this->get_default_property(self :: PROPERTY_TRANSLATION);
	}

	/**
	 * Sets the translation of this VariableTranslation.
	 * @param translation
	 */
	function set_translation($translation)
	{
		$this->set_default_property(self :: PROPERTY_TRANSLATION, $translation);
	}

	/**
	 * Returns the date of this VariableTranslation.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this VariableTranslation.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}

	/**
	 * Returns the user_id of this VariableTranslation.
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
	 * Sets the user_id of this VariableTranslation.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

	/**
	 * Returns the rating of this VariableTranslation.
	 * @return the rating.
	 */
	function get_rating()
	{
		return $this->get_default_property(self :: PROPERTY_RATING);
	}

	/**
	 * Sets the rating of this VariableTranslation.
	 * @param rating
	 */
	function set_rating($rating)
	{
		$this->set_default_property(self :: PROPERTY_RATING, $rating);
	}

	/**
	 * Returns the rated of this VariableTranslation.
	 * @return the rated.
	 */
	function get_rated()
	{
		return $this->get_default_property(self :: PROPERTY_RATED);
	}

	/**
	 * Sets the rated of this VariableTranslation.
	 * @param rated
	 */
	function set_rated($rated)
	{
		$this->set_default_property(self :: PROPERTY_RATED, $rated);
	}

	/**
	 * Returns the status of this VariableTranslation.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}

	/**
	 * Sets the status of this VariableTranslation.
	 * @param status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

	function get_relative_rating()
	{
		return (int)($this->get_rating() / $this->get_rated());
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	function is_locked()
	{
		return $this->get_status() == self :: STATUS_BLOCKED;
	}

	function lock()
	{
		$this->set_status(self :: STATUS_BLOCKED);
	}

	function unlock()
	{
		$this->set_status(self :: STATUS_NORMAL);
	}

	function switch_lock()
	{
		if ($this->is_locked())
		{
			$this->unlock();
		}
		else
		{
			$this->lock();
		}
	}

	function is_outdated()
	{
	    return ($this->get_status() == self :: STATUS_OUTDATED);
	}

	function get_status_icon()
	{
	    switch($this->get_status())
	    {
	        case self :: STATUS_NORMAL :
	            $label = 'TranslationVerified';
	            $image = Theme :: get_image_path() . 'status_normal.png';
	            break;
	        case self :: STATUS_OUTDATED :
	            $label = 'TranslationOutdated';
	            $image = Theme :: get_image_path() . 'status_outdated.png';
	            break;
	        case self :: STATUS_BLOCKED :
	            $label = 'TranslationLocked';
	            $image = Theme :: get_image_path() . 'status_locked.png';
	            break;
	    }

	    $toolbar_item = new ToolbarItem(Translation :: get($label), $image, null, ToolbarItem :: DISPLAY_ICON);

	    return $toolbar_item->as_html();
	}

	function verify()
	{
	    $this->set_status(self :: STATUS_NORMAL);
	    return parent :: update();
	}

	function deprecate()
	{
	    $this->set_status(self :: STATUS_OUTDATED);
	    return parent :: update();
	}

	function update()
	{
		$original_translation = $this->get_data_manager()->retrieve_variable_translation($this->get_id());

		if (($original_translation->get_translation() != $this->get_translation()) && $original_translation != ' ')
		{
			$historic_variable_translation = new HistoricVariableTranslation();
			$historic_variable_translation->set_variable_translation_id($this->get_id());
			$historic_variable_translation->set_translation($original_translation->get_translation());
			$historic_variable_translation->set_date($original_translation->get_date());
			$historic_variable_translation->set_user_id($original_translation->get_user_id());
			$historic_variable_translation->set_rating($original_translation->get_rating());
			$historic_variable_translation->set_rated($original_translation->get_rated());

			if (!$historic_variable_translation->create())
			{
				return false;
			}

			$this->set_rating(0);
			$this->set_rated(0);
			$this->set_status(self :: STATUS_OUTDATED);
		}

		return parent :: update();
	}
}

?>