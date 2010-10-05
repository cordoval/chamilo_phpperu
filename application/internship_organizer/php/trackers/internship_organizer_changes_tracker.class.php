<?php

class InternshipOrganizerChangesTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;
    
    const CREATE_EVENT = 'create';
    const UPDATE_EVENT = 'update';
    const DELETE_EVENT = 'delete';
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_OBJECT_ID = 'object_id';
    const PROPERTY_OBJECT_TYPE = 'object_type';
    const PROPERTY_EVENT_TYPE = 'event_type';
    const PROPERTY_CREATED = 'created';
    
    const TYPE_AGREEMENT = 1;
    const TYPE_AGREEMENT_REL_LOCATION = 2;
    const TYPE_AGREEMENT_REL_MENTOR = 3;
    const TYPE_AGREEMENT_REL_USER = 4;
    const TYPE_APPOINTMENT = 5;
    const TYPE_CATEGORY = 6;
    const TYPE_CATEGORY_REL_LOCATION = 7;
    const TYPE_CATEGORY_REL_PERIOD = 8;
    const TYPE_LOCATION = 9;
    const TYPE_MENTOR = 10;
    const TYPE_MENTOR_REL_LOCATION = 11;
    const TYPE_MENTOR_REL_USER = 12;
    const TYPE_MOMENT = 13;
    const TYPE_ORGANISATION = 14;
    const TYPE_ORGANISATION_REL_USER = 15;
    const TYPE_PERIOD = 16;
    const TYPE_PERIOD_REL_GROUP = 17;
    const TYPE_PERIOD_REL_USER = 18;
    const TYPE_PUBLICATION = 19;
    const TYPE_REGION = 20;

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_object_id($parameters[self :: PROPERTY_OBJECT_ID]);
        $this->set_object_type($parameters[self :: PROPERTY_OBJECT_TYPE]);
        $this->set_event_type($parameters[self :: PROPERTY_EVENT_TYPE]);
        $this->set_created(time());
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_CREATED, self :: PROPERTY_EVENT_TYPE, self :: PROPERTY_OBJECT_ID, self :: PROPERTY_OBJECT_TYPE));    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_OBJECT_ID);
    }

    function set_object_id($object_id)
    {
        $this->set_default_property(self :: PROPERTY_OBJECT_ID, $object_id);
    }

    function get_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_OBJECT_TYPE);
    }

    function set_object_type($object_type)
    {
        $this->set_default_property(self :: PROPERTY_OBJECT_TYPE, $object_type);
    }

    function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    function set_created($date)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $date);
    }

    function get_event_type()
    {
        return $this->get_default_property(self :: PROPERTY_EVENT_TYPE);
    }

    function set_event_type($event_type)
    {
        $this->set_default_property(self :: PROPERTY_EVENT_TYPE, $event_type);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>