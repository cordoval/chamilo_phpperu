<?php

require_once dirname ( __FILE__ ) . '/../../agreement.class.php';

class DefaultInternshipOrganizerAgreementTableColumnModel extends ObjectTableColumnModel {
	
		
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerAgreementTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$dm = InternshipOrganizerDataManager :: get_instance();
        $agreement_alias = $dm->get_alias(InternshipOrganizerAgreement :: get_table_name());
       	$user_alias = UserDataManager::get_instance()->get_alias(User :: get_table_name());
		
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( User::PROPERTY_FIRSTNAME, false, $user_alias);
		$columns [] = new ObjectTableColumn ( User::PROPERTY_LASTNAME, false, $user_alias);
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_NAME, true, $agreement_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_DESCRIPTION, true , $agreement_alias);
		$columns [] = new ObjectTableColumn ( Translation :: get('InternshipOrganizerPeriodName'), false );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_BEGIN, true,$agreement_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_END, true, $agreement_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_STATUS, true , $agreement_alias);
		
		return $columns;
	}
}
?>