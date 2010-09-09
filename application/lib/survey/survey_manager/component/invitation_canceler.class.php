<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

class SurveyManagerInvitationCancelerComponent extends SurveyManager
{
    
    const MESSAGE_INVITATION_CANCELED = 'InvitationCanceled';
    const MESSAGE_INVITATION_NOT_CANCELED = 'InvitationNotCanceled';
    const MESSAGE_INVITATIONS_CANCELED = 'InvitationsCanceled';
    const MESSAGE_INVITATIONS_NOT_CANCELED = 'InvitationsNotCanceled';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_INVITEES);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $invitee_ids = explode('|', $id);
                $publication_id = $invitee_ids[0];
                $location = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
                $invitee = $invitee_ids[1];
                
                if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_EDIT, $publication_id, SurveyRights :: TYPE_PUBLICATION, $this->get_user_id()))
                {
                   $failures ++;
                }
                else
                {
                	if (! RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_VIEW, $invitee, $location, 0))
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = self :: MESSAGE_INVITATION_NOT_CANCELED;
                }
                else
                {
                    $message = self :: MESSAGE_INVITATIONS_NOT_CANCELED;
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = self :: MESSAGE_INVITATION_CANCELED;
                }
                else
                {
                    $message = self :: MESSAGE_INVITATIONS_CANCELED;
                }
            }
            
           
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PARTICIPANTS, SurveyManager :: PARAM_SURVEY_PUBLICATION =>$publication_id));
           
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyInviteesSelected')));
        }
    }

}
?>