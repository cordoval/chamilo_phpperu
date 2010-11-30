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
}

?>
