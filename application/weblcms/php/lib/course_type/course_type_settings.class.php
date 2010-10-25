<?php
/**
 * $Id: course_type_settings.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course_type
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_settings.class.php';

class CourseTypeSettings extends CourseSettings
{
	const CLASS_NAME = __CLASS__;

	const PROPERTY_COURSE_TYPE_ID = "course_type_id";
	const PROPERTY_TITULAR_FIXED = "titular_fixed";
	const PROPERTY_LANGUAGE_FIXED = 'language_fixed';
	const PROPERTY_VISIBILITY_FIXED = 'visibility_fixed';
	const PROPERTY_ACCESS_FIXED = 'access_fixed';
	const PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED = 'max_number_of_members_fixed';

	/**
	 * Get the default properties of all coursetypes.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(
		array(self :: PROPERTY_COURSE_TYPE_ID,
			self :: PROPERTY_TITULAR_FIXED,
			self :: PROPERTY_LANGUAGE_FIXED,
			self :: PROPERTY_VISIBILITY_FIXED,
			self :: PROPERTY_ACCESS_FIXED,
			self :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED));
	}	

	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return WeblcmsDataManager :: get_instance();
	}

	/**
	 * Returns the languages fixed of this coursetype object
	 * @return Boolean the languages fixed
	 */
	function get_course_type_id()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
	}

	/**
	 * Returns the titular fixed of this coursetype object
	 * @return Boolean the titular fixed
	 */
	function get_titular_fixed()
	{
		return $this->get_default_property(self :: PROPERTY_TITULAR_FIXED);
	}
	
	/**
	 * Returns the languages fixed of this coursetype object
	 * @return Boolean the languages fixed
	 */
	function get_language_fixed()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE_FIXED);
	}

	/**
	 * Returns the visibility fixed of this coursetype object
	 * @return boolean the visibility fixed
	 */
	function get_visibility_fixed()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY_FIXED);
	}

	/**
	 * Returns the access fixed of this coursetype object
	 * @return boolean the access fixed
	 */
	function get_access_fixed()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_FIXED);
	}

	/**
	 * Returns the max number of members fixed of this coursetype object
	 * @return Boolean the max number of members fixed
	 */
	function get_max_number_of_members_fixed()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED);
	}

	/**
	 * Sets the coursetype id of this coursetype object
	 * @param int $course_type_id the coursetype id
	 */
	function set_course_type_id($course_type_id)
	{
		return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
	}

	/**
	 * Sets the titular fixed of this coursetype object
	 * @param Boolean $titular_fixed the titular fixed
	 */
	function set_titular_fixed($titular_fixed)
	{
		return $this->set_default_property(self :: PROPERTY_TITULAR_FIXED, $titular_fixed);
	}
	
	/**
	 * Sets the languages fixed of this coursetype object
	 * @param Boolean $languages_fixed the languages fixed
	 */
	function set_language_fixed($language_fixed)
	{
		return $this->set_default_property(self :: PROPERTY_LANGUAGE_FIXED, $language_fixed);
	}

	/**
	 * Sets the visibility fixed of this coursetype object
	 * @param Boolean $visibility_fixed the visibility fixed
	 */
	function set_visibility_fixed($visibility_fixed)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILITY_FIXED, $visibility_fixed);
	}

	/**
	 * Sets the access fixed of this coursetype object
	 * @param Boolean $access the access fixed
	 */
	function set_access_fixed($access_fixed)
	{
		$this->set_default_property(self :: PROPERTY_ACCESS_FIXED, $access_fixed);
	}

	/**
	 * Sets the the max number of members fixed of this coursetype object
	 * @param Boolean $max_number_of_members_fixed the max number of members fixed
	 */
	function set_max_number_of_members_fixed($max_number_of_members_fixed)
	{
		$this->set_default_property(self :: PROPERTY_MAX_NUMBER_OF_MEMBERS_FIXED, $max_number_of_members_fixed);
	}
	//    function set_max_number_of_admin($max_number_of_admin)
	//    {
	//            $this->set_default_property(self :: PROPERTY_MAX_NUMBER_OF_ADMIN, $max_number_of_admin);
	//    }

	//    /**
	//     * Creates the course type object in persistent storage
	//     * @return boolean
	//     */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();

		if (! $wdm->create_course_type_settings($this))
		{
			return false;
		}

		return true;
	}
	//
	//    function create_course_type_all()
	//    {
	//        $wdm = WeblcmsDataManager :: get_instance();
	//        return $wdm->create_course_type_all($this);
	//    }
	//

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	/**
	 * Checks whether the given user is a course type admin
	 * @param User $user
	 * @return boolean
	 */
	function is_course_type_admin($user)
	{
		if ($user->is_platform_admin())
		{
			return true;
		}
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->is_course_type_admin($this, $user->get_id());
	}
}
?>