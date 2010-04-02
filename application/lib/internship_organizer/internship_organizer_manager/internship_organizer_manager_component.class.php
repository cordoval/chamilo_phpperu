<?php

abstract class InternshipOrganizerManagerComponent extends WebApplicationComponent {
	
	/**
	 * Constructor
	 * @param InternshipOrganizer $internship_organizer The internship_organizer which
	 * provides this component
	 */
	function InternshipOrganizerManagerComponent($internship_organizer) {
		parent::__construct ( $internship_organizer );
	}
	
	function get_organisation_application_url() {
		return $this->get_parent ()->get_organisation_application_url ();
	}
	
	function get_agreement_application_url() {
		return $this->get_parent ()->get_agreement_application_url ();
	}
	
	function get_category_application_url() {
		return $this->get_parent ()->get_category_application_url ();
	}

}
?>