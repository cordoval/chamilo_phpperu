<?php

class SurveyManagerTakerComponent extends SurveyManager
{
    private $publication_id;
    private $invitee_id;

    function run()
    {
        
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $this->publication_id);
        
        $this->set_parameter(SurveyManager :: PARAM_PARTICIPANT_ID, Request :: get(SurveyManager :: PARAM_PARTICIPANT_ID));
        
        $this->invitee_id = Request :: get(SurveyManager :: PARAM_INVITEE_ID);
        
        if ($this->invitee_id)
        {
            $this->user_id = $this->invitee_id;
            $this->set_parameter(SurveyManager :: PARAM_INVITEE_ID, $this->invitee_id);
        }
        else
        {
            $this->user_id = $this->get_user_id();
        }
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->user_id))
        {
            Display :: not_allowed();
        }
        
        ComplexDisplay :: launch(Survey :: get_type_name(), $this, false);
    }
}

?>