<?php
namespace repository\content_object\survey_page;

use repository\ComplexBuilderComponent;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyPageBuilderViewerComponent extends SurveyPageBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>