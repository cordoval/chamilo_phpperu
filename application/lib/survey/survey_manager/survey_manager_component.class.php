<?php
/**
 * $Id: survey_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.survey.survey_manager
 */

/**
 * Basic functionality of a component to talk with the survey application
 *
 * @author Sven Vanpoucke
 * @author
 */
abstract class SurveyManagerComponent extends WebApplicationComponent {
	
	/**
	 * Constructor
	 * @param Survey $survey The survey which
	 * provides this component
	 */
	function SurveyManagerComponent($survey_manager) {
		parent::__construct ( $survey_manager );
	}
	
	//Data Retrieval
	

	function count_survey_participant_trackers($condition) {
		return $this->get_parent ()->count_survey_participant_trackers ( $condition );
	}
	
	function retrieve_survey_participant_trackers($condition = null, $offset = null, $count = null, $order_property = null) {
		return $this->get_parent ()->retrieve_survey_participant_trackers ( $condition, $offset, $count, $order_property );
	}
	
	function count_survey_publications($condition) {
		return $this->get_parent ()->count_survey_publications ( $condition );
	}
	
	function retrieve_survey_publications($condition = null, $offset = null, $count = null, $order_property = null) {
		return $this->get_parent ()->retrieve_survey_publications ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_survey_publication($id) {
		return $this->get_parent ()->retrieve_survey_publication ( $id );
	}
	
	function count_survey_publication_groups($condition) {
		return $this->get_parent ()->count_survey_publication_groups ( $condition );
	}
	
	function retrieve_survey_publication_groups($condition = null, $offset = null, $count = null, $order_property = null) {
		return $this->get_parent ()->retrieve_survey_publication_groups ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_survey_publication_group($id) {
		return $this->get_parent ()->retrieve_survey_publication_group ( $id );
	}
	
	function count_survey_publication_users($condition) {
		return $this->get_parent ()->count_survey_publication_users ( $condition );
	}
	
	function retrieve_survey_publication_users($condition = null, $offset = null, $count = null, $order_property = null) {
		return $this->get_parent ()->retrieve_survey_publication_users ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_survey_publication_user($id) {
		return $this->get_parent ()->retrieve_survey_publication_user ( $id );
	}
	
	function count_survey_publication_mails($condition) {
		return $this->get_parent ()->count_survey_publication_mails ( $condition );
	}
	
	function retrieve_survey_publication_mails($condition = null, $offset = null, $count = null, $order_property = null) {
		return $this->get_parent ()->retrieve_survey_publication_mails ( $condition, $offset, $count, $order_property );
	}
	
	function retrieve_survey_publication_mail($id) {
		return $this->get_parent ()->retrieve_survey_publication_mail ( $id );
	}
	
	// Url Creation
	

	function get_create_survey_publication_url() {
		return $this->get_parent ()->get_create_survey_publication_url ();
	}
	
	function get_update_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_update_survey_publication_url ( $survey_publication );
	}
	
	function get_delete_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_delete_survey_publication_url ( $survey_publication );
	}
	
	function get_browse_survey_publications_url() {
		return $this->get_parent ()->get_browse_survey_publications_url ();
	}
	
	function get_manage_survey_publication_categories_url() {
		return $this->get_parent ()->get_manage_survey_publication_categories_url ();
	}
	
//	function get_browse_test_survey_publication_url() {
//		return $this->get_parent ()->get_browse_test_survey_publication_url ();
//	}
//	
//	function get_browse_test_survey_participants_url($survey_publication) {
//		return $this->get_parent ()->get_browse_test_survey_participants_url ( $survey_publication );
//	}
	
	function get_survey_publication_viewer_url($survey_publication) {
		return $this->get_parent ()->get_survey_publication_viewer_url ( $survey_publication );
	}
	
	function get_testcase_url() {
		return $this->get_parent ()->get_testcase_url ();
	}
	
//	function get_test_survey_publication_viewer_url($survey_participant) {
//		return $this->get_parent ()->get_test_survey_publication_viewer_url ( $survey_participant );
//	}
	
	function get_survey_results_viewer_url($survey_publication) {
		return $this->get_parent ()->get_survey_results_viewer_url ( $survey_publication );
	}
	
	function get_import_survey_url() {
		return $this->get_parent ()->get_import_survey_url ();
	}
	
	function get_export_survey_url($survey_publication) {
		return $this->get_parent ()->get_export_survey_url ( $survey_publication );
	}
	
	function get_change_survey_publication_visibility_url($survey_publication) {
		return $this->get_parent ()->get_change_survey_publication_visibility_url ( $survey_publication );
	}
	
	function get_move_survey_publication_url($survey_publication) {
		return $this->get_parent ()->get_move_survey_publication_url ( $survey_publication );
	}
	
	function get_results_exporter_url($tracker_id) {
		return $this->get_parent ()->get_results_exporter_url ( $tracker_id );
	}
	
	function get_download_documents_url($survey_publication) {
		return $this->get_parent ()->get_download_documents_url ( $survey_publication );
	}
	
	function get_mail_survey_participant_url($survey_publication) {
		return $this->get_parent ()->get_mail_survey_participant_url ( $survey_publication );
	}
	
	function get_build_survey_url($survey_publication) {
		return $this->get_parent ()->get_build_survey_url ( $survey_publication );
	}
}
?>