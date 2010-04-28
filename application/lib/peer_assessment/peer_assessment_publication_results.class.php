<?php
/**
 * This class describes the PeerAssessmentPublicationResults data object
 *
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationResults extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * PeerAssessmentPublicationResults properties
     */
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_COMPETENCE_ID = 'competence_id';
    const PROPERTY_INDICATOR_ID = 'indicator_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_GRADED_USER_ID = 'graded_user_id';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FINISHED = 'finished';


    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_COMPETENCE_ID, self :: PROPERTY_INDICATOR_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_GRADED_USER_ID, self :: PROPERTY_SCORE, self :: PROPERTY_FINISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PeerAssessmentDataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this PeerAssessmentPublicationResults.
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this PeerAssessmentPublicationResults.
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the competence_id of this PeerAssessmentPublicationResults.
     * @return the competence_id.
     */
    function get_competence_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMPETENCE_ID);
    }

    /**
     * Sets the competence_id of this PeerAssessmentPublicationResults.
     * @param competence_id
     */
    function set_competence_id($competence_id)
    {
        $this->set_default_property(self :: PROPERTY_COMPETENCE_ID, $competence_id);
    }

    /**
     * Returns the indicator_id of this PeerAssessmentPublicationResults.
     * @return the indicator_id.
     */
    function get_indicator_id()
    {
        return $this->get_default_property(self :: PROPERTY_INDICATOR_ID);
    }

    /**
     * Sets the category of this PeerAssessmentPublicationResults.
     * @param category
     */
    function set_indicator_id($indicator_id)
    {
        $this->set_default_property(self :: PROPERTY_INDICATOR_ID, $indicator_id);
    }

    /**
     * Returns the user_id of this PeerAssessmentPublicationResults.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }   

    /**
     * Sets the from_date of this PeerAssessmentPublicationResults.
     * @param from_date
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the graded_user_id of this PeerAssessmentPublicationResults.
     * @return the graded_user_id.
     */
    function get_graded_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_GRADED_USER_ID);
    }

    /**
     * Sets the graded_user_id of this PeerAssessmentPublicationResults.
     * @param graded_user_id
     */
    function set_graded_user_id($graded_user_id)
    {
        $this->set_default_property(self :: PROPERTY_GRADED_USER_ID, $graded_user_id);
    }
    
	/**
     * Returns the score of this PeerAssessmentPublicationResults.
     * @return the score.
     */
    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    /**
     * Sets the score of this PeerAssessmentPublicationResults.
     * @param score
     */
    function set_score($score)
    {
        $this->set_default_property(self :: PROPERTY_SCORE, $score);
    }

    /**
     * Returns the finished of this PeerAssessmentPublicationResults.
     * @return the finished.
     */
    function get_finished()
    {
        return $this->get_default_property(self :: PROPERTY_FINISHED);
    }

    /**
     * Sets the finished of this PeerAssessmentPublicationResults.
     * @param finished
     */
    function set_finished($finished)
    {
        $this->set_default_property(self :: PROPERTY_FINISHED, $finished);
    }    
    
    /**
     * Swtiches the finished function.
     */   
	function toggle_finished()
    {
        $this->set_finished(! $this->get_finished());
    }
    
	/**
     * Determines whether this publication is finished or not
     * @return boolean True if the publication is finished.
     */
    function is_finished()
    {
        return $this->get_default_property(self :: PROPERTY_FINISHED);
    }

    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>