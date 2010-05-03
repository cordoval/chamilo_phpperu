<?php

class SurveyContextTemplateSubscribePageBrowserTableDataProvider extends ObjectTableDataProvider {
	
	function SurveyContextTemplateSubscribePageBrowserTableDataProvider($browser, $condition) {
		parent::__construct ( $browser, $condition );
	}
	
	function get_objects($offset, $count, $order_property = null) {
		return InternshipOrganizerDataManager::get_instance ()->retrieve_pages ( $this->get_condition (), $offset, $count, $order_property );
	}
	
	function get_object_count() {
		return InternshipOrganizerDataManager::get_instance ()->count_pages ( $this->get_condition () );
	}
}
?>