<?php
class InternshipOrganizerAgreement extends DataClass {
	const CLASS_NAME = __CLASS__;
	
	/**
	 * InternshipAgreement properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_BEGIN = 'begin';
	const PROPERTY_END = 'end';
	const PROPERTY_PERIOD_ID = 'period_id';
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_STUDENT_ID = 'student_id'; //owner
	const PROPERTY_STATUS = 'status';
	
	const STATUS_NEW = 1;
	const STATUS_COACH = 2;
	const STATUS_LOCATION = 3;
	const STATUS_MENTOR = 4;
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names() {
		return array (	self::PROPERTY_ID, 
						self::PROPERTY_NAME, 
						self::PROPERTY_DESCRIPTION, 
						self::PROPERTY_BEGIN, 
						self::PROPERTY_END, 
						self::PROPERTY_PERIOD_ID,
						self::PROPERTY_LOCATION_ID,
						self::PROPERTY_STUDENT_ID,
						self::PROPERTY_STATUS );
	}
	
	function get_data_manager() {
		return InternshipOrganizerDataManager::get_instance ();
	}
	
	/**
	 * Returns the id of this InternshipAgreement.
	 * @return the id.
	 */
	function get_id() {
		return $this->get_default_property ( self::PROPERTY_ID );
	}
	
	/**
	 * Sets the id of this InternshipAgreement.
	 * @param id
	 */
	function set_id($id) {
		$this->set_default_property ( self::PROPERTY_ID, $id );
	}
	
	/**
	 * Returns the name of this InternshipAgreement.
	 * @return the name.
	 */
	function get_name() {
		return $this->get_default_property ( self::PROPERTY_NAME );
	}
	
	/**
	 * Sets the name of this InternshipAgreement.
	 * @param name
	 */
	function set_name($name) {
		$this->set_default_property ( self::PROPERTY_NAME, $name );
	}
	
	/**
	 * Returns the description of this InternshipAgreement.
	 * @return the description.
	 */
	function get_description() {
		return $this->get_default_property ( self::PROPERTY_DESCRIPTION );
	}
	
	/**
	 * Sets the description of this InternshipAgreement.
	 * @param description
	 */
	function set_description($description) {
		$this->set_default_property ( self::PROPERTY_DESCRIPTION, $description );
	}
	
	/**
	 * Returns the begin of this InternshipAgreement.
	 * @return begin.
	 */
	function get_begin() {
		return $this->get_default_property ( self::PROPERTY_BEGIN );
	}
	
	/**
	 * Sets the begin of this InternshipAgreement.
	 * @param begin
	 */
	function set_begin($begin) {
		$this->set_default_property ( self::PROPERTY_BEGIN, $begin );
	}
	
	/**
	 * Returns the end of this InternshipAgreement.
	 * @return end.
	 */
	function get_end() {
		return $this->get_default_property ( self::PROPERTY_END );
	}
	
	/**
	 * Sets the end of this InternshipAgreement.
	 * @param end
	 */
	function set_end($end) {
		$this->set_default_property ( self::PROPERTY_END, $end );
	}	
	/**
	 * Returns the period_id of this InternshipAgreement.
	 * @return period_id.
	 */
	function get_period_id() {
		return $this->get_default_property ( self::PROPERTY_PERIOD_ID );
	}
	
	/**
	 * Sets the period_id of this InternshipAgreement.
	 * @param period_id
	 */
	function set_period_id($period_id) {
		$this->set_default_property ( self::PROPERTY_PERIOD_ID, $period_id );
	}
	/**
	 * Returns the location_id of this InternshipAgreement.
	 * @return location_id.
	 */
	function get_location_id() {
		return $this->get_default_property ( self::PROPERTY_LOCATION_ID );
	}
	
	/**
	 * Sets the location_id of this InternshipAgreement.
	 * @param location_id
	 */
	function set_location_id($location_id) {
		$this->set_default_property ( self::PROPERTY_LOCATION_ID, $location_id );
	}	
	
	/**
	 * Returns the student_id of this InternshipAgreement.
	 * @return student_id.
	 */
	function get_student_id() {
		return $this->get_default_property ( self::PROPERTY_STUDENT_ID );
	}
	
	/**
	 * Sets the student_id of this InternshipAgreement.
	 * @param student_id
	 */
	function set_student_id($student_id) {
		$this->set_default_property ( self::PROPERTY_STUDENT_ID, $student_id );
	}
	
	/**
	 * Returns the status of this InternshipAgreement.
	 * @return status.
	 */
	function get_status() {
		return $this->get_default_property ( self::PROPERTY_STATUS );
	}
	
	/**
	 * Sets the status of this InternshipAgreement.
	 * @param status
	 */
	function set_status($status) {
		$this->set_default_property ( self::PROPERTY_STATUS, $status );
	}	
	
	
	function get_status_name($index){
	switch ($index)
        {
            case 1 :
                return Translation :: get('InternshipOrganizerNewAgreement');
//                break;
            case 2 :
                return Translation :: get('InternshipOrganizerCoachAgreement');
//                break;
            case 3 :
                return Translation :: get('InternshipOrganizerLocationAgreement');
//                break;
            case 4 :
                return Translation :: get('InternshipOrganizerMentorAgreement');
//                break;
            default :
                //no default
                break;
        }
	}
	
	static function get_table_name() {
		return 'agreement';
//		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
	
	}
}
