<?php
/**
 * @package application.lib.internship_organizer.internship_organizer_manager
 */

require_once dirname ( __FILE__ ) . '/internship_organizer_manager_component.class.php';
require_once dirname ( __FILE__ ) . '/../internship_organizer_data_manager.class.php';
require_once dirname ( __FILE__ ) . '/../internship_organizer_utilities.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/organisation_manager/organisation_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/organisation_manager/organisation_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/category_manager/category_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/category_manager/category_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/agreement_manager/agreement_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/agreement_manager/agreement_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/region_manager/region_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/region_manager/region_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/mentor_manager/mentor_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/mentor_manager/mentor_manager_component.class.php';

class InternshipOrganizerManager extends WebApplication {
	const APPLICATION_NAME = 'internship_organizer';
	
	const ACTION_ORGANISATION = 'organisation';
	const ACTION_AGREEMENT = 'agreement';
	const ACTION_CATEGORY = 'category';
	const ACTION_APPLICATION_CHOOSER = 'chooser';
	const ACTION_REGION = 'region';
	const ACTION_MENTOR = 'mentor';		
	/**
	 * Constructor
	 * @param User $user The current user
	 */
	function InternshipOrganizerManager($user = null) {
		parent::__construct ( $user );
		$this->parse_input_from_table ();
	}
	
	/**
	 * Run this internship_organizer manager
	 */
	function run() {
		$action = $this->get_action ();
		$component = null;
		switch ($action) {
			case self::ACTION_ORGANISATION :
				$component = InternshipOrganizerManagerComponent::factory ( 'Organisation', $this );
				break;
			case self::ACTION_AGREEMENT :
				$component = InternshipOrganizerManagerComponent::factory ( 'Agreement', $this );
				break;
			case self::ACTION_CATEGORY :
				$component = InternshipOrganizerManagerComponent::factory ( 'Category', $this );
				break;
			case self::ACTION_APPLICATION_CHOOSER :
				$component = InternshipOrganizerManagerComponent::factory ( 'ApplicationChooser', $this );
				break;
			case self::ACTION_REGION :
				$component = InternshipOrganizerManagerComponent::factory ( 'Region', $this );
				break;
			case self::ACTION_MENTOR :
				$component = InternshipOrganizerManagerComponent::factory ( 'Mentor', $this );
				break;
			default :
				$this->set_action ( self::ACTION_APPLICATION_CHOOSER );
				$component = InternshipOrganizerManagerComponent::factory ( 'ApplicationChooser', $this );
		
		}
		$component->run ();
	}
	
	function get_organisation_application_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_ORGANISATION ) );
	
	}
	
	function get_agreement_application_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_AGREEMENT ) );
	
	}
	
	function get_category_application_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_CATEGORY ) );
	
	}
	function get_region_application_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_REGION ) );
	
	}
	function get_mentor_application_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_MENTOR ) );
	
	}
	private function parse_input_from_table() {
		//not used jet
	}
	
	function get_application_name() {
		return self::APPLICATION_NAME;
	}

}
?>