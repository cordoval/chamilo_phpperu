<?php

namespace user;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\SuccessRestMessage;

class UserWebserviceHandler
{
    function get_list($data)
    { 
        return UserDataManager :: get_instance()->retrieve_users()->as_array();
    }

    function get($id)
    {
        return UserDataManager :: get_instance()->retrieve_user($id);
    }

    function create($data)
    {
        $message = new SuccessRestMessage();
        $message->set_success(false);
        $message->set_message(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('User')), Utilities :: COMMON_LIBRARIES));
        return $message;
    }

    function update($id, $data)
    {

    }

    function delete($id)
    {

    }
}

?>
