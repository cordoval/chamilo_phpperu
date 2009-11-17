<?php
/**
 * $Id: validator.class.php 182 2009-11-13 09:14:43Z vanpouckesven $
 * @package common.validator
 */
require_once Path :: get_application_path() . 'lib/weblcms/validator/course_validator.class.php';

/**
 * This is the abstract validator class. It is to be a base class for specific validators
 * for User/Group/Course/Reporting webservices.
 * It also doubles as a factory for the aforementioned specific validators.
 * Furthermore, it has an error message and an error source, which can be used,
 * if need be, to create a detailed SOAP fault error message.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
abstract class Validator
{
    protected $errorMessage;
    protected $errorSource;

    public static function get_validator($type)
    {
        switch ($type)
        {
            case 'user' :
                return new UserValidator();
            case 'group' :
                return new GroupValidator();
            case 'course' :
                return new CourseValidator();
            case 'reporting' :
                return new ReportingValidator();
        }
    }

    function get_error_message()
    {
        return $this->errorMessage;
    }

    function get_error_source()
    {
        return $this->errorSource;
    }

    abstract function validate_retrieve(&$object);

    abstract function validate_create(&$object);

    abstract function validate_update(&$object);

    abstract function validate_delete(&$object);

    public function validate_properties($properties, $requiredProperties)
    {
        foreach ($requiredProperties as $property)
        {
            if ($properties[$property] == null)
            {
                $this->errorMessage = Translation :: get('Property') . ' ' . $property . ' ' . Translation :: get('IsNotPresentButRequired');
                return false;
            }
        }
        return true;
    }

    public function validate_property_names($properties, $defaultProperties)
    {
        foreach ($properties as $property => $value)
        {
            if (! in_array($property, array_keys($defaultProperties)))
            {
                $this->errorMessage = Translation :: get('Property') . ' ' . $property . ' ' . Translation :: get('IsNotAValidPropertyName');
                return false;
            }
        }
        return true;
    }

}
?>
