<?php

namespace admin;

use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * @package admin.lib
 * @author Hans De Bisschop
 */
class MessageTemplate extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USE_CHAMILO_HEADERS = 'use_chamilo_headers';
    const PROPERTY_NAME = 'name';
    const PROPERTY_MESSAGE = 'message';
    const PROPERTY_APPLICATION = 'application';

    /**
     * Get the default properties of all objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
            self :: PROPERTY_USE_CHAMILO_HEADERS,
            self :: PROPERTY_NAME,
            self :: PROPERTY_MESSAGE,
            self :: PROPERTY_APPLICATION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the use chamilo headers from this message template.
     * @return boolean - if the chamilo headers are used
     */
    function get_use_chamilo_headers()
    {
        return $this->get_default_property(self :: PROPERTY_USE_CHAMILO_HEADERS);
    }

    /**
     * Returns the name of this message template.
     * @return String - the name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the message of this message template.
     * @return String - the message
     */
    function get_message()
    {
        return $this->get_default_property(self :: PROPERTY_MESSAGE);
    }

    /**
     * Returns the application of this message template.
     * @return String - the application
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Sets the use chamilo headers of this message template.
     * @param boolean - $use_chamilo_headers
     */
    function set_use_chamilo_headers($use_chamilo_headers)
    {
        $this->set_default_property(self :: PROPERTY_USE_CHAMILO_HEADERS, $use_chamilo_headers);
    }

    /**
     * Sets the name of this message template.
     * @param String - $name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the user of this message template.
     * @param String - $message
     */
    function set_message($message)
    {
        $this->set_default_property(self :: PROPERTY_MESSAGE, $message);
    }

    /**
     * Sets the application of this message template.
     * @param String $application
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

}

?>