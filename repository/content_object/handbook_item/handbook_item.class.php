<?php
require_once dirname(__FILE__) . '/../handbook/uuid.class.php';


class HandbookItem extends ContentObject implements Versionable
{
    const PROPERTY_REFERENCE = 'reference_id';
    const PROPERTY_UUID = 'uuid';
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_REFERENCE, self :: PROPERTY_UUID);
    }

    function get_reference()
    {
        return $this->get_additional_property(self :: PROPERTY_REFERENCE);
    }

    function set_reference($reference)
    {
        $this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
    }

    function get_uuid()
    {
        return $this->get_additional_property(self :: PROPERTY_UUID);
    }

    function set_uuid()
    {
        $uuid = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
        $this->set_additional_property(self :: PROPERTY_UUID, $uuid);
    }

    function create()
    {
        $this->set_uuid();
        parent::create();
    }
}
?>