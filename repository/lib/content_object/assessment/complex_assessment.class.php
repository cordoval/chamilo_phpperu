<?php
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
        $allowed_types[] = OrderingQuestion :: get_type_name();
        return $allowed_types;
    }
}
?>