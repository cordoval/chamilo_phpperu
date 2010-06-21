<?php
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipOrganizerAgreementManagerMoverComponent extends InternshipOrganizerAgreementManager {
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() {
		$agreement_id = Request::get ( InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID );
		$location_id = Request::get ( InternshipOrganizerAgreementManager::PARAM_LOCATION_ID );
		$move_direction = Request::get ( InternshipOrganizerAgreementManager::PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION );
				
		$datamanager = InternshipOrganizerDataManager::get_instance ();
		$agreement_rel_location = $datamanager->retrieve_agreement_rel_location($location_id, $agreement_id);
		if ($agreement_rel_location->move ( $move_direction )) {
			
			if($move_direction < 0){
							$message = htmlentities ( Translation::get ( 'InternshipOrganizerAgreementRelLocationPreferenceUp' ) );
				
			}else{
							$message = htmlentities ( Translation::get ( 'InternshipOrganizerAgreementRelLocationPreferenceDown' ) );
				
			}
			
		}
		
		$this->redirect ($message , false, array (InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager::ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID => $agreement_id ) );
		exit ();
	
	}
}
?>