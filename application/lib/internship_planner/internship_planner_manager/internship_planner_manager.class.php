<?php
/**
 * @package application.lib.internship_planner.internship_planner_manager
 */
require_once dirname ( __FILE__ ) . '/internship_planner_manager_component.class.php';
require_once dirname ( __FILE__ ) . '/../internship_planner_data_manager.class.php';
require_once dirname ( __FILE__ ) . '/../internship_planner_utilities.class.php';

require_once Path::get_application_path () . 'lib/internship_planner/organisation_manager/organisation_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_planner/organisation_manager/organisation_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_planner/category_manager/category_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_planner/category_manager/category_manager_component.class.php';

require_once Path::get_application_path () . 'lib/internship_planner/agreement_manager/agreement_manager.class.php';
require_once Path::get_application_path () . 'lib/internship_planner/agreement_manager/agreement_manager_component.class.php';


class InternshipPlannerManager extends WebApplication {
	const APPLICATION_NAME = 'internship_planner';
	
	const ACTION_ORGANISATION = 'organisation';
	const ACTION_AGREEMENT = 'agreement';
	const ACTION_CATEGORY = 'category';
	const ACTION_APPLICATION_CHOOSER = 'chooser';
	
	/**
	 * Constructor
	 * @param User $user The current user
	 */
	function InternshipPlannerManager($user = null) {
		parent::__construct ( $user );
		$this->parse_input_from_table ();
	}
	
	/**
	 * Run this internship_planner manager
	 */
	function run() {
		$action = $this->get_action ();
		$component = null;
		switch ($action) {
			case self::ACTION_ORGANISATION :
				$component = InternshipPlannerManagerComponent::factory ( 'Organisation', $this );
				break;
			case self::ACTION_AGREEMENT :
				$component = InternshipPlannerManagerComponent::factory ( 'Agreement', $this );
				break;
			case self::ACTION_CATEGORY :
				$component = InternshipPlannerManagerComponent::factory ( 'Category', $this );
				break;
			case self::ACTION_APPLICATION_CHOOSER :
				$component = InternshipPlannerManagerComponent::factory ( 'ApplicationChooser', $this );
				break;
			default :
				$this->set_action ( self::ACTION_APPLICATION_CHOOSER );
				$component = InternshipPlannerManagerComponent::factory ( 'ApplicationChooser', $this );
		
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
	
	private function parse_input_from_table() {
		//not used jet
	}
	
	function get_application_name() {
		return self::APPLICATION_NAME;
	}

}
?>