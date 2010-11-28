<?php

namespace user;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\RestMessage;

class UserWebserviceHandler
{
    function get_list($data)
    {
        $message = RestMessage :: factory(RestMessage :: TYPE_SUCCESS);
        $message->set_success(true);
        $message->set_message(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('User')), Utilities :: COMMON_LIBRARIES));
        return $message;
    }

    function get($id, $data)
    {

    }

    function create($id, $data)
    {

    }

    function update($id, $data)
    {

    }

    function delete($id, $data)
    {
        
    }
}

?>
