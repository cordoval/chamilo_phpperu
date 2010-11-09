<?php
namespace repository\content_object\adaptive_assessment;

use repository\ContentObjectForm;

/**
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/adaptive_assessment.class.php';

class AdaptiveAssessmentForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new AdaptiveAssessment();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>