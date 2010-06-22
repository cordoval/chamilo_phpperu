<?php
/**
 * $Id: criteria.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.criteria
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/criteria_option.class.php';

/**
 * This class represents an criteria
 */
class Criteria extends ContentObject implements Versionable
{
    const PROPERTY_OPTIONS = 'options';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;
        return $this->set_options($options);
    }

    public function set_options($options)
    {
        return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
    }

    public function get_options()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_OPTIONS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_OPTIONS);
    }
}
?>