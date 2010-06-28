<?php
require_once dirname(__FILE__) . '/internship_organizer_reporting_block.class.php';

abstract class InternshipOrganizerAgreementReportingBlock extends InternshipOrganizerReportingBlock
{

    function get_user_id()
    {
        return $this->get_parent()->get_parameter(UserManager :: PARAM_USER_USER_ID);
    }

    function get_agreement_ids($user_types)
    {
        
        if (! is_array($user_types))
        {
            $user_types = array($user_types);
        }
        
        $conditions = array();
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $conditions[] = new EqualityCondition(User :: PROPERTY_ID, $this->get_user_id(), $user_alias, true);
        $conditions[] = new InCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $user_types, InternshipOrganizerAgreementRelUser :: get_table_name());
        $condition = new AndCondition($conditions);
        $agreements = InternshipOrganizerDataManager :: get_instance()->retrieve_agreements($condition);
        $agreement_ids = array();
        while ($agreement = $agreements->next_result())
        {
            $agreement_ids[] = $agreement->get_id();
        }
        
        if (in_array(InternshipOrganizerUserType :: MENTOR, $user_types))
        {
            
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $condition = new EqualityCondition(User :: PROPERTY_ID, $this->get_user_id(), $user_alias, true);
            $agreements = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor_agreements($condition);
            while ($agreement = $agreements->next_result())
            {
                $agreement_ids[] = $agreement->get_id();
            }
        }
        
        return array_unique($agreement_ids);
    }

    function get_condition($agreement_type, $user_types)
    {
        $conditions = array();
        $agreement_ids = $this->get_agreement_ids($user_types);
        if (count($agreement_ids) > 0)
        {
            $conditions[] = new InCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $agreement_ids);
            $conditions[] = new InCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, InternshipOrganizerUserType :: STUDENT, InternshipOrganizerAgreementRelUser :: get_table_name());
            $conditions[] = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_STATUS, $agreement_type);
            return new AndCondition($conditions);
        }
        else
        {
            return new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, 0);
        }
    }

}
?>