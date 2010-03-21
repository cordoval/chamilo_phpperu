<?php

class TestcaseManagerComponent extends SubManagerComponent
{

	function get_browse_survey_publication_url() {
		return $this->get_parent()->get_browse_survey_publication_url();
	}
	
	function get_browse_survey_participants_url($survey_publication) {
		return $this->get_parent()->get_browse_survey_participants_url($survey_publication);
	}
	
	function get_survey_publication_viewer_url($survey_participant) {
		return $this->get_parent()->get_survey_publication_viewer_url($survey_participant);
	}
}
?>