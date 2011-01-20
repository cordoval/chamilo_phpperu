<?php
namespace repository\content_object\assessment;

use repository\content_object\ordering_question\OrderingQuestion;
use repository\content_object\assessment_match_text_question\AssessmentMatchTextQuestion;
use repository\content_object\assessment_match_numeric_question\AssessmentMatchNumericQuestion;
use repository\content_object\match_question\MatchQuestion;
use repository\content_object\assessment_matrix_question\AssessmentMatrixQuestion;
use repository\content_object\assessment_select_question\AssessmentSelectQuestion;
use repository\content_object\assessment_matching_question\AssessmentMatchingQuestion;
use repository\content_object\assessment_multiple_choice_question\AssessmentMultipleChoiceQuestion;
use repository\content_object\fill_in_blanks_question\FillInBlanksQuestion;
use repository\content_object\hotspot_question\HotspotQuestion;
use repository\content_object\assessment_open_question\AssessmentOpenQuestion;
use repository\content_object\assessment_rating_question\AssessmentRatingQuestion;

use repository\ComplexContentObjectItem;
/**
 * $Id: complex_assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.assessment
 *
 */
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = AssessmentRatingQuestion :: get_type_name();
        $allowed_types[] = AssessmentOpenQuestion :: get_type_name();
        $allowed_types[] = HotspotQuestion :: get_type_name();
        $allowed_types[] = FillInBlanksQuestion :: get_type_name();
        $allowed_types[] = AssessmentMultipleChoiceQuestion :: get_type_name();
        $allowed_types[] = AssessmentMatchingQuestion :: get_type_name();
        $allowed_types[] = AssessmentSelectQuestion :: get_type_name();
        $allowed_types[] = AssessmentMatrixQuestion :: get_type_name();
        $allowed_types[] = MatchQuestion :: get_type_name();
        $allowed_types[] = AssessmentMatchNumericQuestion :: get_type_name();
        $allowed_types[] = AssessmentMatchTextQuestion :: get_type_name();
        $allowed_types[] = OrderingQuestion :: get_type_name();
        return $allowed_types;
    }
}
?>