<?php
/**
 * $Id: handbook.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.handbook
 */
/**
 * This class represents an handbook
 */
class Handbook extends ContentObject implements ComplexContentObjectSupport {
    const CLASS_NAME = __CLASS__;
    const PROPERTY_UUID = 'uuid';

    static function get_additional_property_names() {
        return array(self :: PROPERTY_UUID);
    }

    static function get_type_name() {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    function get_allowed_types() {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }

    function get_uuid() {
        return $this->get_additional_property(self :: PROPERTY_UUID);
    }

    function set_uuid($uuid) {
        $this->set_additional_property(self :: PROPERTY_UUID, $uuid);
    }
}
?>