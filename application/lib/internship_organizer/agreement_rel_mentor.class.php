<?php
/**
 * This class describes a InternshipAgreementRelMentor data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerAgreementRelMentor extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipAgreementRelMentor properties
     */
    const PROPERTY_AGREEMENT_ID = 'agreement_id';
    const PROPERTY_MENTOR_ID = 'mentor_id';
  
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_MENTOR_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the agreement_id of this InternshipAgreementRelMentor.
     * @return the agreement_id.
     */
    function get_agreement_id()
    {
        return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
    }

    /**
     * Sets the agreement_id of this InternshipAgreementRelMentor.
     * @param agreement_id
     */
    function set_agreement_id($agreement_id)
    {
        $this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
    }

    /**
     * Returns the mentor_id of this InternshipAgreementRelMentor.
     * @return the mentor_id.
     */
    function get_mentor_id()
    {
        return $this->get_default_property(self :: PROPERTY_MENTOR_ID);
    }

    /**
     * Sets the mentor_id of this InternshipAgreementRelMentor.
     * @param mentor_id
     */
    function set_mentor_id($mentor_id)
    {
        $this->set_default_property(self :: PROPERTY_MENTOR_ID, $mentor_id);
    }
    
    static function get_table_name()
    {
        return 'agreement_rel_mentor';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>