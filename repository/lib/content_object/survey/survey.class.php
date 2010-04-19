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
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
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
        $allowed_types[] = SurveyPage :: get_type_name();
        return $allowed_types;
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
	function is_versionable()
    {
        return false;
    }
	
    function get_pages(){
     	
    	$complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
        
     	$survey_page_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $survey_page_ids[] = $complex_content_object->get_ref();
        }
        
        if(count($survey_page_ids) == 0){
        	$survey_page_ids[] = 0;
        }
        
        
        $condition = new InCondition(ContentObject :: PROPERTY_ID, $survey_page_ids, ContentObject :: get_table_name());
        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
    }
    
    function count_pages(){
    	return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    }
    
}
?>