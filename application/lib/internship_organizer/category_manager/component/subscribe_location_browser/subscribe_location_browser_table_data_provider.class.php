<?php

class SubscribeLocationBrowserTableDataProvider extends ObjectTableDataProvider {
	
	function SubscribeLocationBrowserTableDataProvider($browser, $condition) {
		parent::__construct ( $browser, $condition );
	}
	
	/**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
	function get_objects($offset, $count, $order_property = null) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_locations ( $this->get_condition (), $offset, $count, $order_property );
	}
	
	function get_object_count() {
		return InternshipOrganizerDataManager::get_instance ()->count_locations ( $this->get_condition () );
	}
}
?>