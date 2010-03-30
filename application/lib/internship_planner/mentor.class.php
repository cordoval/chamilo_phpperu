<?php 
/**
 * internship_planner
 */

/**
 * This class describes a Mentor data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerMentor extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Mentor properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_FIRSTNAME = 'firstname';
	const PROPERTY_LASTNAME = 'lastname';
	const PROPERTY_EMAIL = 'email';
	const PROPERTY_TELEPHONE = 'telephone';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_LASTNAME, self :: PROPERTY_EMAIL, self :: PROPERTY_TELEPHONE, self :: PROPERTY_USER_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Mentor.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Mentor.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the title of this Mentor.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Mentor.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}

	/**
	 * Returns the firstname of this Mentor.
	 * @return the firstname.
	 */
	function get_firstname()
	{
		return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
	}

	/**
	 * Sets the firstname of this Mentor.
	 * @param firstname
	 */
	function set_firstname($firstname)
	{
		$this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
	}

	/**
	 * Returns the lastname of this Mentor.
	 * @return the lastname.
	 */
	function get_lastname()
	{
		return $this->get_default_property(self :: PROPERTY_LASTNAME);
	}

	/**
	 * Sets the lastname of this Mentor.
	 * @param lastname
	 */
	function set_lastname($lastname)
	{
		$this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
	}

	/**
	 * Returns the email of this Mentor.
	 * @return the email.
	 */
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}

	/**
	 * Sets the email of this Mentor.
	 * @param email
	 */
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
	}

	/**
	 * Returns the telephone of this Mentor.
	 * @return the telephone.
	 */
	function get_telephone()
	{
		return $this->get_default_property(self :: PROPERTY_TELEPHONE);
	}

	/**
	 * Sets the telephone of this Mentor.
	 * @param telephone
	 */
	function set_telephone($telephone)
	{
		$this->set_default_property(self :: PROPERTY_TELEPHONE, $telephone);
	}

	/**
	 * Returns the user_id of this Mentor.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Mentor.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>