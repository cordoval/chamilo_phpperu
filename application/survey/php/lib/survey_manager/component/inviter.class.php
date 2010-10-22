<?php namespace application\survey;
class SurveyManagerInviterComponent extends SurveyManager
{

    function run()
    {
        $invitation_manager = new InvitationManager($this);
        $invitation_manager->run();
    }
}
?>