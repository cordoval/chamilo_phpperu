<?php
namespace application\personal_messenger;

use common\libraries\Block;
use common\libraries\Redirect;

/**
 * @author Hans De bisschop
 */

class PersonalMessengerBlock extends Block
{
    function get_publication_viewing_link($personal_message)
    {
        $parameters = array();
        $parameters[PersonalMessengerManager :: PARAM_ACTION] = PersonalMessengerManager :: ACTION_VIEW_PUBLICATION;
        $parameters[PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID] = $personal_message->get_id();
        
        return Redirect :: get_link(PersonalMessengerManager :: APPLICATION_NAME, $parameters, array(), false, Redirect :: TYPE_APPLICATION);
    }
}
?>