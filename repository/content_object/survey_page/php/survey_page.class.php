<?php
namespace repository\content_object\survey_page;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\ComplexContentObjectSupport;
use repository\ComplexContentObjectItem;

use repository\content_object\survey_rating_question\SurveyRatingQuestion;
use repository\content_object\survey_open_question\SurveyOpenQuestion;
use repository\content_object\survey_multiple_choice_question\SurveyMultipleChoiceQuestion;
use repository\content_object\survey_matching_question\SurveyMatchingQuestion;
use repository\content_object\survey_select_question\SurveySelectQuestion;
use repository\content_object\survey_matrix_question\SurveyMatrixQuestion;
use repository\content_object\survey_description\SurveyDescription;
use repository\RepositoryDataManager;

use repository\ContentObject;

/**
 * $Id: survey_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_page
 */
/**
 * This class represents an assessment
 */
class SurveyPage extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_INTRODUCTION_TEXT = 'intro_text';
    const PROPERTY_CONFIG = 'config';
    
    const FROM_VISIBLE_QUESTION_ID = 'from_visible_question_id';
    const TO_VISIBLE_QUESTIONS_IDS = 'to_visible_question_ids';
    const ANSWERMATCHES = 'answer_matches';
    
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_INTRODUCTION_TEXT, self :: PROPERTY_CONFIG);
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

    function get_config()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_CONFIG)))
        {
            return $result;
        }
        return array();
    }

    function set_config($value)
    {
        $this->set_additional_property(self :: PROPERTY_CONFIG, serialize($value));
    }

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = SurveyRatingQuestion :: get_type_name();
        $allowed_types[] = SurveyOpenQuestion :: get_type_name();
        $allowed_types[] = SurveyMultipleChoiceQuestion :: get_type_name();
        $allowed_types[] = SurveyMatchingQuestion :: get_type_name();
        $allowed_types[] = SurveySelectQuestion :: get_type_name();
        $allowed_types[] = SurveyMatrixQuestion :: get_type_name();
        $allowed_types[] = SurveyDescription :: get_type_name();
        
        return $allowed_types;
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    
    }

    function get_questions($complex = false)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        if ($complex)
        {
            return $complex_content_objects;
        }
        $questions = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $questions[] = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
        }
        return $questions;
    
    }

    function count_questions()
    {
        return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    }

    function get_question_ids($complex = false)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
        $question_ids = array();
        
        if ($complex)
        {
            while ($complex_content_object = $complex_content_objects->next_result())
            {
                $question_ids[] = $complex_content_object->get_id();
            }
        
        }
        else
        {
            while ($complex_content_object = $complex_content_objects->next_result())
            {
                $question = $this->get_data_manager()->retrieve_content_object($complex_content_object->get_ref());
                //                if ($question->get_type() != SurveyDescription::get_type_name())
                //                {
                $question_ids[] = $question->get_id();
            
     //                }
            

            }
        }
        
        return $question_ids;
    
    }

}
?>