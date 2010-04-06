<?php
require_once Path::get_application_path () . 'lib/internship_organizer/organisation_manager/component/browser/browser_table.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';
require_once Path::get_application_path () . 'lib/internship_organizer/organisation.class.php';

class InternshipOrganizerOrganisationManager extends SubManager {
	
	const PARAM_ACTION = 'action';
	const PARAM_ORGANISATION_ID = 'organisation_id';
	const PARAM_DELETE_SELECTED_ORGANISATIONS = 'delete_organisations';
	
	const PARAM_LOCATION_ID = 'location_id';
	const PARAM_DELETE_SELECTED_LOCATIONS = 'delete_locations';
	
	const ACTION_CREATE_ORGANISATION = 'create';
	const ACTION_BROWSE_ORGANISATION = 'browse';
	const ACTION_UPDATE_ORGANISATION = 'update';
	const ACTION_DELETE_ORGANISATION = 'delete';
	const ACTION_VIEW_ORGANISATION = 'view';
	
	const ACTION_CREATE_LOCATION = 'create_location';
	const ACTION_BROWSE_LOCATIONS = 'browse_locations';
	const ACTION_EDIT_LOCATION = 'edit_location';
	const ACTION_DELETE_LOCATION = 'delete_location';
	
	function InternshipOrganizerOrganisationManager($internship_manager) {
		parent::__construct ( $internship_manager );
		$action = Request::get ( self::PARAM_ACTION );
		if ($action) {
			$this->set_parameter ( self::PARAM_ACTION, $action );
		}
		$this->parse_input_from_table ();
	
	}
	
	function run() {
		$action = $this->get_parameter ( self::PARAM_ACTION );
		
		switch ($action) {
			
			case self::ACTION_UPDATE_ORGANISATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Updater', $this );
				break;
			case self::ACTION_DELETE_ORGANISATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Deleter', $this );
				break;
			case self::ACTION_CREATE_ORGANISATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Creator', $this );
				break;
			case self::ACTION_VIEW_ORGANISATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Viewer', $this );
				break;
			case self::ACTION_BROWSE_ORGANISATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Browser', $this );
				break;
			case self::ACTION_EDIT_LOCATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'LocationUpdater', $this );
				break;
			case self::ACTION_DELETE_LOCATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'LocationDeleter', $this );
				break;
			case self::ACTION_CREATE_LOCATION :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'LocationCreator', $this );
				break;
			case self::ACTION_BROWSE_LOCATIONS :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'LocationBrowser', $this );
				break;
			default :
				$component = InternshipOrganizerOrganisationManagerComponent::factory ( 'Browser', $this );
				break;
		}
		
		$component->run ();
	}
	
	function get_application_component_path() {
		return Path::get_application_path () . 'lib/internship_organizer/organisation_manager/component/';
	}
	
	//organisations
	

	function count_organisations($condition) {
		return InternshipOrganizerDataManager::get_instance ()->count_organisations ( $condition );
	}
	
	function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_organisations ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_organisation($id) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_organisation ( $id );
	}
	
	//locations
	function count_locations($condition) {
		return InternshipOrganizerDataManager::get_instance ()->count_locations ( $condition );
	}
	
	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_locations ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_location($id) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_location ( $id );
	}
	
	//url creation
	function get_create_organisation_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_CREATE_ORGANISATION ) );
	}
	
	function get_update_organisation_url($organisation) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_UPDATE_ORGANISATION, self::PARAM_ORGANISATION_ID => $organisation->get_id () ) );
	}
	
	function get_delete_organisation_url($organisation) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_DELETE_ORGANISATION, self::PARAM_ORGANISATION_ID => $organisation->get_id () ) );
	}
	
	function get_browse_organisations_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_BROWSE_ORGANISATION ) );
	}
	
	function get_view_organisation_url($organisation) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_VIEW_ORGANISATION, self::PARAM_ORGANISATION_ID => $organisation->get_id () ) );
	}
	
	function get_create_location_url($organisation) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_CREATE_LOCATION, self::PARAM_ORGANISATION_ID => $organisation->get_id () ) );
	}
	
	function get_update_location_url($location) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_EDIT_LOCATION, self::PARAM_LOCATION_ID => $location->get_id () ) );
	}
	
	function get_delete_location_url($location) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_DELETE_LOCATION, self::PARAM_LOCATION_ID => $location->get_id () ) );
	}
	
	function get_browse_locations_url() {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_BROWSE_LOCATIONS ) );
	}
	
	private function parse_input_from_table() {
		
		if (isset ( $_POST ['action'] )) {
			
			if (isset ( $_POST [InternshipOrganizerOrganisationBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX] )) {
				$selected_ids = $_POST [InternshipOrganizerOrganisationBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX];
			}
			
			if (isset ( $_POST [InternshipOrganizerLocationBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX] )) {
				$selected_ids = $_POST [InternshipOrganizerLocationBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX];
			}
			
			if (empty ( $selected_ids )) {
				$selected_ids = array ();
			} elseif (! is_array ( $selected_ids )) {
				$selected_ids = array ($selected_ids );
			}
			switch ($_POST ['action']) {
				case self::PARAM_DELETE_SELECTED_LOCATIONS :
					$this->set_organisation_action ( self::ACTION_DELETE_LOCATION );
					$_GET [self::PARAM_LOCATION_ID] = $selected_ids;
					break;
				case self::PARAM_DELETE_SELECTED_ORGANISATIONS :
					$this->set_organisation_action ( self::ACTION_DELETE_ORGANISATION );
					$_GET [self::PARAM_ORGANISATION_ID] = $selected_ids;
					break;
			}
		}
	}
	
	private function set_organisation_action($action) {
		$this->set_parameter ( self::PARAM_ACTION, $action );
	}
}

?>