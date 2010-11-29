<?php

namespace common\libraries;

/**
 * A rest message that has a true or false to determine wether a manipulation action has been successfull
 */

class SuccessRestMessage extends DataClass
{
    const PROPERTY_SUCCESS = 'success';
    const PROPERTY_MESSAGE = 'message';

    function get_success()
    {
        return $this->get_default_property(self :: PROPERTY_SUCCESS);
    }

    function set_success($success)
    {
        $this->set_default_property(self :: PROPERTY_SUCCESS, $success);
    }

    function get_success_as_string()
    {
        return $this->get_success() ? 'true' : 'false';
    }

    function get_message()
    {
        return $this->get_default_property(self :: PROPERTY_MESSAGE);
    }

    function set_message($message)
    {
        $this->set_default_property(self :: PROPERTY_MESSAGE, $message);
    }

    function get_table_name()
    {

    }

    function get_data_manager()
    {
        
    }
}

?>
