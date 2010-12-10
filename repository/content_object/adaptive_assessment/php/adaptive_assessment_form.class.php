<?php
namespace repository\content_object\adaptive_assessment;

use repository\ContentObjectForm;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new AdaptiveAssessment();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>