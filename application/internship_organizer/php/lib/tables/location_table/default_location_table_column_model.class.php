<?php
namespace application\internship_organizer;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\Translation;

/** @author Steven Willaert */

require_once dirname ( __FILE__ ) . '/../../location.class.php';

class DefaultInternshipOrganizerLocationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$dm = InternshipOrganizerDataManager :: get_instance();
        $region_alias = $dm->get_alias(InternshipOrganizerRegion :: get_table_name());
        $location_alias = $dm->get_alias(InternshipOrganizerLocation :: get_table_name());
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_NAME, true, $location_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_ADDRESS, true, $location_alias );
		
		$region_column = new ObjectTableColumn ( InternshipOrganizerRegion::PROPERTY_ZIP_CODE, true, $region_alias );
		$region_column->set_title(Translation :: get('ZipCode'));
		$columns [] = $region_column;
		
		$region_column = new ObjectTableColumn ( InternshipOrganizerRegion::PROPERTY_CITY_NAME, true, $region_alias );
		$region_column->set_title(Translation :: get('City'));
		$columns [] = $region_column;
		
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_DESCRIPTION, true, $location_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_EMAIL, true, $location_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_TELEPHONE, true, $location_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_FAX, true, $location_alias );
		
		return $columns;
	}
}
?>