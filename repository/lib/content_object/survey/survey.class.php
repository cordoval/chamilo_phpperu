<?php
/**
 * $Id: survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents an survey
 */

require_once (dirname(__FILE__) . '/survey_context.class.php');

class Survey extends ContentObject
{
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_INTRODUCTION_TEXT = 'intro_text';
    const PROPERTY_ANONYMOUS = 'anonymous';
    const PROPERTY_CONTEXT = 'context';
    
    private $context;

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_INTRODUCTION_TEXT, self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_ANONYMOUS, self :: PROPERTY_CONTEXT);
    }

    function get_introduction_text()
    {
        return $this->get_additional_property(self :: PROPERTY_INTRODUCTION_TEXT);
    }

    function set_introduction_text($text)
    {
        $this->set_additional_property(self :: PROPERTY_INTRODUCTION_TEXT, $text);
    }

    function get_finish_text()
    {
        return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
    }

    function set_finish_text($value)
    {
        $this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
    }

    function get_anonymous()
    {
        return $this->get_additional_property(self :: PROPERTY_ANONYMOUS);
    }

    function set_anonymous($value)
    {
        return $this->set_additional_property(self :: PROPERTY_ANONYMOUS, $value);
    }

    function get_context()
    {
        $type = $this->get_additional_property(self :: PROPERTY_CONTEXT);
        return SurveyContext :: factory($type);
    }

    function set_context($value)
    {
        $this->set_additional_property(self :: PROPERTY_CONTEXT, $value);
    }

    function set_context_instance($context)
    {
        $this->context = $context;
    }

    function get_context_instance()
    {
        return $this->context;
    }

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = 'survey_page';
        return $allowed_types;
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>