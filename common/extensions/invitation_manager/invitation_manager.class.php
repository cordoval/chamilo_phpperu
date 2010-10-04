<?php
/**
 * @author Hans De Bisschop
 */

class InvitationManager extends SubManager
{
    const CLASS_NAME = __CLASS__;

    const PARAM_ACTION = 'invitation_action';
    const PARAM_INVITATION_CODE = 'invitation_code';

    const ACTION_CREATE_INVITATIONS = 'create';

    function InvitationManager($application)
    {
        parent :: __construct($application);
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);

        switch ($action)
        {
            case self :: ACTION_CREATE_INVITATIONS :
                $component = $this->create_component('Creator');
                break;
            default :
                $component = $this->create_component('Creator');
                $this->set_parameter(self :: PARAM_ACTION, self :: ACTION_CREATE_INVITATIONS);
                break;
        }

        $component->run();
    }

	function get_application_component_path()
	{
		return Path :: get_common_extensions_path() . 'invitation_manager/component/';
	}
}
?>