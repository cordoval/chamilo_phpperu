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
        
        $dm = $this->get_data_manager();
        
        switch ($location_type)
        {
            case 1 :
                
                //If 1 relation is set to aprove, all reletions have to be of approve type and the status of the agreement has to be in "to approve"
                

                $query = 'UPDATE ' . $dm->escape_table_name('agreement_rel_location') . ' SET ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE) . '= 1 WHERE ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID) . '=' . $dm->quote($this->get_agreement_id());
                $res = $dm->query($query);
                $res->free();
                
                //if mentors from location are allready related to agreement they have to be deleted
                $condition = new EqualityCondition(InternshipOrganizerMentorRelLocation :: PROPERTY_LOCATION_ID, $this->get_location_id());
                $mentor_rel_locations = $dm->retrieve_mentor_rel_locations($condition);
                $mentor_ids = array();
                while ($mentor_rel_location = $mentor_rel_locations->next_result())
                {
                    $mentor_ids = $mentor_rel_location->get_mentor_id();
                }
                
                $conditions = array();
                $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $this->get_agreement_id());
                $conditions[] = new InCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, $mentor_ids);
                $condition = new AndCondition($conditions);
                
                $dm->delete(InternshipOrganizerAgreementRelMentor :: get_table_name(), $condition);
                
                $this->get_agreement()->update_status();
                break;
            case 2 :
                
                // if 1  relation get approved all other relations has to be in type denied and the agreement status has to be updated to "approved"
                

                $query = 'UPDATE ' . $dm->escape_table_name('agreement_rel_location') . ' SET ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE) . '= 3 WHERE ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID) . '=' . $dm->quote($this->get_agreement_id()) . 'AND NOT' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID) . ' =' . $dm->quote($this->get_location_id());
                $res = $dm->query($query);
                $res->free();
                
                $query = 'UPDATE ' . $dm->escape_table_name('agreement_rel_location') . ' SET ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE) . '= 2 WHERE ' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID) . '=' . $dm->quote($this->get_agreement_id()) . 'AND' . $dm->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID) . '=' . $dm->quote($this->get_location_id());
                $res = $dm->query($query);
                $res->free();
                
                $this->get_agreement()->update_status();
                break;
            case 3 :
                
                //a relation will never be set in type denied alone, this type will only exist when there is just 1 other relation that is in type approved, all others have to be in type denied
                

                break;
            default :
                //no default
                break;
        }
        
        $this->set_default_property(self :: PROPERTY_LOCATION_TYPE, $location_type);
    }

    function get_agreement()
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($this->get_agreement_id());
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

    function move($move_direction)
    {
        return InternshipOrganizerDataManager :: get_instance()->move_agreement_rel_location($this, $move_direction);
    }

    static function get_table_name()
    {
        return 'agreement_rel_location';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>