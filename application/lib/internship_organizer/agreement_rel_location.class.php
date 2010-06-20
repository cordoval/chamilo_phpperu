<?php
/**
 * This class describes a InternshipAgreementRelLocation data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerAgreementRelLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const TO_APPROVE = 1;
    const APPROVED = 2;
    const DENIED = 3;
    
    /**
     * InternshipAgreementRelAgreement properties
     */
    const PROPERTY_AGREEMENT_ID = 'agreement_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_LOCATION_TYPE = 'location_type';
    const PROPERTY_PREFERENCE_ORDER = 'preference_order';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_LOCATION_ID, self :: PROPERTY_LOCATION_TYPE, self :: PROPERTY_PREFERENCE_ORDER);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the agreement_id of this InternshipAgreementRelAgreement.
     * @return the agreement_id.
     */
    function get_agreement_id()
    {
        return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
    }

    /**
     * Sets the agreement_id of this InternshipAgreementRelAgreement.
     * @param agreement_id
     */
    function set_agreement_id($agreement_id)
    {
        $this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
    }

    /**
     * Returns the agreement_id of this InternshipAgreementRelAgreement.
     * @return the agreement_id.
     */
    function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    /**
     * Sets the agreement_id of this InternshipAgreementRelAgreement.
     * @param agreement_id
     */
    function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
    }

    /**
     * Returns the location_type of this InternshipAgreementRelAgreement.
     * @return the location_type.
     */
    function get_location_type()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_TYPE);
    }

    /**
     * Sets the location_type of this InternshipAgreementRelAgreement.
     * @param location_type
     */
    function set_location_type($location_type)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_TYPE, $location_type);
    }

    /**
     * Returns the $preference_order of this InternshipAgreementRelAgreement.
     * @return the $preference_order.
     */
    function get_preference_order()
    {
        return $this->get_default_property(self :: PROPERTY_PREFERENCE_ORDER);
    }

    /**
     * Sets the $preference_order of this InternshipAgreementRelAgreement.
     * @param $preference_order
     */
    function set_preference_order($preference_order)
    {
        $this->set_default_property(self :: PROPERTY_PREFERENCE_ORDER, $preference_order);
    }

    static function get_table_name()
    {
        return 'agreement_rel_location';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>