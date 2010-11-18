<?php

namespace application\personal_messenger;

use common\libraries\Block;

/**
 * @author Hans De bisschop
 */

class PersonalMessengerBlock extends Block
{
    function get_publication_viewing_link($personal_message)
    {
        return $this->get_link(PersonalMessengerManager :: APPLICATION_NAME, array(PersonalMessengerManager :: PARAM_ACTION => PersonalMessengerManager :: ACTION_VIEW_PUBLICATION, PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID => $personal_message->get_id()));
    }
}
?>