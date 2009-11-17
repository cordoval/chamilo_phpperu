<?php
/**
 * $Id: assessment_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.assessment.assessment_manager
 */

/**
 * Basic functionality of a component to talk with the assessment application
 *
 * @author Sven Vanpoucke
 * @author
 */
abstract class AssessmentManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param Assessment $assessment The assessment which
     * provides this component
     */
    function AssessmentManagerComponent($assessment)
    {
        parent :: __construct($assessment);
    }

    //Data Retrieval
    

    function count_assessment_publications($condition)
    {
        return $this->get_parent()->count_assessment_publications($condition);
    }

    function retrieve_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_assessment_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication($id)
    {
        return $this->get_parent()->retrieve_assessment_publication($id);
    }

    function count_assessment_publication_groups($condition)
    {
        return $this->get_parent()->count_assessment_publication_groups($condition);
    }

    function retrieve_assessment_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_assessment_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication_group($id)
    {
        return $this->get_parent()->retrieve_assessment_publication_group($id);
    }

    function count_assessment_publication_users($condition)
    {
        return $this->get_parent()->count_assessment_publication_users($condition);
    }

    function retrieve_assessment_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_assessment_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication_user($id)
    {
        return $this->get_parent()->retrieve_assessment_publication_user($id);
    }

    // Url Creation
    

    function get_create_assessment_publication_url()
    {
        return $this->get_parent()->get_create_assessment_publication_url();
    }

    function get_update_assessment_publication_url($assessment_publication)
    {
        return $this->get_parent()->get_update_assessment_publication_url($assessment_publication);
    }

    function get_delete_assessment_publication_url($assessment_publication)
    {
        return $this->get_parent()->get_delete_assessment_publication_url($assessment_publication);
    }

    function get_browse_assessment_publications_url()
    {
        return $this->get_parent()->get_browse_assessment_publications_url();
    }

    function get_manage_assessment_publication_categories_url()
    {
        return $this->get_parent()->get_manage_assessment_publication_categories_url();
    }

    function get_assessment_publication_viewer_url($assessment_publication)
    {
        return $this->get_parent()->get_assessment_publication_viewer_url($assessment_publication);
    }

    function get_assessment_results_viewer_url($assessment_publication)
    {
        return $this->get_parent()->get_assessment_results_viewer_url($assessment_publication);
    }

    function get_import_qti_url()
    {
        return $this->get_parent()->get_import_qti_url();
    }

    function get_export_qti_url($assessment_publication)
    {
        return $this->get_parent()->get_export_qti_url($assessment_publication);
    }

    function get_change_assessment_publication_visibility_url($assessment_publication)
    {
        return $this->get_parent()->get_change_assessment_publication_visibility_url($assessment_publication);
    }

    function get_move_assessment_publication_url($assessment_publication)
    {
        return $this->get_parent()->get_move_assessment_publication_url($assessment_publication);
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_parent()->get_results_exporter_url($tracker_id);
    }

    function get_download_documents_url($assessment_publication)
    {
        return $this->get_parent()->get_download_documents_url($assessment_publication);
    }

    function get_publish_survey_url($assessment_publication)
    {
        return $this->get_parent()->get_publish_survey_url($assessment_publication);
    }
    
    function get_build_assessment_url($assessment_publication)
    {
    	return $this->get_parent()->get_build_assessment_url($assessment_publication);
    }
}
?>