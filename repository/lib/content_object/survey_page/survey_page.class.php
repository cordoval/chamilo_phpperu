<?php
/**
 * $Id: survey_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_page
 */
/**
 * This class represents an assessment
 */
class SurveyPage extends ContentObject
{
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_INTRODUCTION_TEXT = 'intro_text';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_INTRODUCTION_TEXT);
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

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = 'survey_rating_question';
        $allowed_types[] = 'survey_open_question';
        $allowed_types[] = 'survey_multiple_choice_question';
        $allowed_types[] = 'survey_matching_question';
        $allowed_types[] = 'survey_select_question';
        $allowed_types[] = 'survey_matrix_question';
        $allowed_types[] = 'survey_description';
        
        return $allowed_types;
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    
    }

    function get_questions()
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        $question_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $question_ids[] = $complex_content_object->get_ref();
        }
        
        $conditions = array();
        $conditions[] = new InCondition(ContentObject :: PROPERTY_ID, $question_ids, ContentObject :: get_table_name());
//        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'survey_description', ContentObject :: get_table_name()));
        return RepositoryDataManager :: get_instance()->retrieve_content_objects(new AndCondition($conditions));
    }

    function count_questions()
    {
        return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    }
}
?>