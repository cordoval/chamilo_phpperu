<?php
/**
 * @package application.lib.internship_organizer.internship_organizer_manager
 */

require_once dirname ( __FILE__ ) . '/../internship_organizer_data_manager.class.php';
require_once dirname ( __FILE__ ) . '/../internship_organizer_utilities.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/organisation_manager/organisation_manager.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/category_manager/category_manager.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/agreement_manager/agreement_manager.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/region_manager/region_manager.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/mentor_manager/mentor_manager.class.php';

require_once Path::get_application_path () . 'lib/internship_organizer/period_manager/period_manager.class.php';

class InternshipOrganizerManager extends WebApplication
{
	const APPLICATION_NAME = 'internship_organizer';
	
	const ACTION_ORGANISATION = 'organisation';
	const ACTION_AGREEMENT = 'agreement';
	const ACTION_CATEGORY = 'category';
	const ACTION_APPLICATION_CHOOSER = 'chooser';
	const ACTION_REGION = 'region';
	const ACTION_MENTOR = 'mentor';		
	const ACTION_PERIOD = 'period';
	
	/**
	 * Constructor
	 * @param User $user The current user
	 */
	function InternshipOrganizerManager($user = null) 
	{
		parent::__construct ( $user );
		$this->parse_input_from_table ();
	}
	
	/**
	 * Run this internship_organizer manager
	 */
	function run() 
	{
		$action = $this->get_action ();
		$component = null;
		BreadcrumbTrail :: get_instance()->add ( new Breadcrumb ( $this->get_url ( array (InternshipOrganizerManager::PARAM_ACTION => InternshipOrganizerManager::ACTION_APPLICATION_CHOOSER) ), Translation::get ( 'InternshipOrganizer' ) ) );
				
		switch ($action) {
			case self::ACTION_ORGANISATION :
				$component = $this->create_component('Organisation');
				break;
			case self::ACTION_AGREEMENT :
				$component = $this->create_component('Agreement');
				break;
			case self::ACTION_CATEGORY :
				$component = $this->create_component('Category');
				break;
			case self::ACTION_APPLICATION_CHOOSER :
				$component = $this->create_component('ApplicationChooser');
				break;
			case self::ACTION_REGION :
				$component = $this->create_component('Region');
				break;
			case self::ACTION_MENTOR :
				$component = $this->create_component('Mentor');
				break;
			case self::ACTION_PERIOD :
				$component = $this->create_component('Period');
				break;
			default :
				$this->set_action ( self::ACTION_APPLICATION_CHOOSER );
				$component = $this->create_component('ApplicationChooser');
								
		}
		
		$component->run ();
	}
	
	function get_organisation_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_ORGANISATION ) );
	
	}
	
	function get_agreement_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_AGREEMENT ) );
	
	}
	
	function get_category_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_CATEGORY ) );
	
	}
	
	function get_application_chooser_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_APPLICATION_CHOOSER ) );
	
	}
	
	function get_region_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_REGION ) );
	
	}
	
	function get_mentor_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_MENTOR ) );
	
	}
	
	function get_period_application_url() 
	{
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_PERIOD ) );
	
	}
	
	private function parse_input_from_table() 
	{
		//not used jet
	}
	
	function get_application_name() 
	{
		return self::APPLICATION_NAME;
	}

}
?>