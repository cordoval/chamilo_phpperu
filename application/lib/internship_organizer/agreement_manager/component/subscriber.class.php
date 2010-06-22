<?php
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipOrganizerAgreementManagerSubscriberComponent extends InternshipOrganizerAgreementManager 
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() 
	{
		$agreement_id = Request::get ( InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID );
		$locations = Request::get ( InternshipOrganizerAgreementManager::PARAM_LOCATION_ID );
		
		$failures = 0;
		
		if (! empty ( $locations )) 
		{
			if (! is_array ( $locations )) 
			{
				$locations = array ($locations );
			}
			
			foreach ( $locations as $location_id ) 
			{
				$existing_agreementrellocation = $this->retrieve_agreement_rel_location ( $location_id, $category_id );
				
				if (! $existing_agreementrellocation) 
				{
					$agreementrellocation = new InternshipOrganizerAgreementRelLocation ();
					$agreementrellocation->set_agreement_id ( $agreement_id );
					$agreementrellocation->set_location_id ( $location_id );
					
										
					if (! $agreementrellocation->create ()) 
					{
						$failures ++;
					}
					//                    else
				//                    {
				//                        Events :: trigger_event('subscribe_location', 'category', array('target_category_id' => $categoryrellocation->get_category_id(), 'target_location_id' => $categoryrellocation->get_location_id(), 'action_user_id' => $this->get_user()->get_id()));
				//                    }
				} else 
				{
					$contains_dupes = true;
				}
			}
			
			//$this->get_result( not good enough?
			if ($failures) 
			{
				if (count ( $locations ) == 1) 
				{
					$message = 'SelectedLocationNotAddedToInternshipOrganizerAgreement' . ($contains_dupes ? 'Dupes' : '');
				} else 
				{
					$message = 'SelectedLocationsNotAddedToInternshipOrganizerAgreement' . ($contains_dupes ? 'Dupes' : '');
				}
			} else 
			{
				if (count ( $locations ) == 1) 
				{
					$message = 'SelectedLocationAddedToInternshipOrganizerAgreement' . ($contains_dupes ? 'Dupes' : '');
				} else 
				{
					$message = 'SelectedLocationsAddedToInternshipOrganizerAgreement' . ($contains_dupes ? 'Dupes' : '');
				}
			}
			
			$this->redirect ( Translation::get ( $message ), ($failures ? true : false), array (InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager::ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer::PARAM_SELECTED_TAB => 'loc_tab' ) );
			exit ();
		} else 
		{
			$this->display_error_page ( htmlentities ( Translation::get ( 'NoInternshipOrganizerAgreementRelLocationSelected' ) ) );
		}
	}
}
?>