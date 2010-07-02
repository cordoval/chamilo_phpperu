<?php
/**
 * @author Hans De Bisschop
 */

class InvitationManager extends SubManager
{
    const CLASS_NAME = __CLASS__;
    
    const PARAM_INVITATION_CODE = 'invitation_code';

    function InvitationManager($application)
    {
        parent :: __construct($application);
    }
    
    function run()
    {
        
    }
    
	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'invitation_manager/component/';
	}
}
?>