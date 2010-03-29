<?php

class SubscribeLocationBrowserTableDataProvider extends ObjectTableDataProvider {
	
	function SubscribeLocationBrowserTableDataProvider($browser, $condition) {
		parent::__construct ( $browser, $condition );
	}
	
	function get_objects($offset, $count, $order_property = null) {
		return InternshipPlannerDataManager::get_instance ()->retrieve_locations ( $this->get_condition (), $offset, $count, $order_property );
	}
	
	function get_object_count() {
		return InternshipPlannerDataManager::get_instance ()->count_locations ( $this->get_condition () );
	}
}
?>