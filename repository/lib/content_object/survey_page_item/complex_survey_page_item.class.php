<?php
/**
 * $Id: survey_page_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_page_item
 */

class ComplexSurveyPageItem extends ComplexContentObjectItem
{
    const PROPERTY_ROUTING = 'routing';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ROUTING);
    }

    function get_routing()
    {
        return $this->get_additional_property(self :: PROPERTY_ROUTING);
    }

    function set_routing($value)
    {
        $this->set_additional_property(self :: PROPERTY_ROUTING, $value);
    }
}
?>