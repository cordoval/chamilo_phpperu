<?php

abstract class TestcaseManagerComponent extends SubManagerComponent {
	
	function get_survey_manager() {
		return $this->get_parent ()->get_survey_manager ();
	}
	
	function get_create_survey_publication_url() {
		return $this->get_parent ()->get_create_survey_publication_url ();
	}
	
	function get_update_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_update_survey_publication_url ( $survey_publication );
	}
	
	function get_delete_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_delete_survey_publication_url ( $survey_publication );
	}
	
	function get_browse_survey_publication_url() {
		return $this->get_parent ()->get_browse_survey_publication_url ();
	}
	
	function get_browse_survey_participants_url($survey_publication) {
		return $this->get_parent ()->get_browse_survey_participants_url ( $survey_publication );
	}
	
	function get_survey_publication_viewer_url($survey_participant) {
		return $this->get_parent ()->get_survey_publication_viewer_url ( $survey_participant );
	}
	
	function get_reporting_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_reporting_survey_publication_url ( $survey_publication );
	}
	
	function get_build_survey_url($survey_publication) {
		return $this->get_parent ()->get_build_survey_url ( $survey_publication );
	}
	
	function get_change_test_to_production_url($survey_publication) {
		return $this->get_parent ()->get_change_test_to_production_url ( $survey_publication );
	}

}
?>